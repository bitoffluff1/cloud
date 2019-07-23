const API_URL = "http://php2";

Vue.component("product-item", {
    props: ["item", "user"],
    data() {
        return {
            url: "/good/good?id=" + this.item.id,
            href: "/good/delete?id=" + this.item.id,
        }
    },
    methods: {
        handleBuyClick(item) {
            this.$emit("onbuy", item);
        }
    },
    template: `
            <div class="item itemAll">
                <div class="fetured-item1">
                    <a :href="url" class="fetured-item">
                        <img :src="item.address" alt="fetured-items">
                        <div class="item-text">
                            <p class="name-item">{{item.name}}</p>
                            <p class="pink-item">\${{item.price}}</p>
                        </div>
                    </a>
                </div>
                <div class="add">
                    <a href="#" class="add-to-card" @click.prevent="handleBuyClick(item.id)">
                        <img class="cart-white" src="img/cart-white.svg" alt="cart">Add to Cart</a>
                </div>
                
                <div class="change-products-box" v-if="user.role == 'isAdmin'">
                    <p class="name-item" v-if="item.stock == '2'">В наличии</p>
                    <p class="text pink" v-else-if="item.stock == '1'">Нет в наличии</p>
                    
                    <form class="change-products" method="post" action="/good/change">
                        <input class="product-form product-form_input"
                               placeholder="New name" type="text" name="name" required>
                        <input type="hidden" name="id" :value="item.id">
                        <input class="product-form product-form_submit" type="submit" value="submit">
                    </form>
                    <form class="change-products" method="post" action="/good/change">
                        <input class="product-form product-form_input"
                               placeholder="New price" type="text" name="price" required>
                        <input type="hidden" name="id" :value="item.id">
                        <input class="product-form product-form_submit" type="submit" value="submit">
                    </form>
                    <a class="product-form product-form_submit product-form_margin"
                       :href="href">delete</a>
                </div> 
            </div>`,
});


Vue.component("products", {
    props: ["items", "query", "quantity", "user"],
    data() {
        return {
            items: [],

            pageNumber: 0,
            quantity: 9,

            user: "",
        };
    },
    computed: {
        filteredItems() {
            if (this.query) {
                const regexp = new RegExp(this.query, "i");
                return this.items.filter((item) => regexp.test(item.name));
            } else {
                return this.items;
            }
        },
        pageCount() {
            let l = this.items.length,
                s = this.quantity;
            return Math.floor(l / s);
        }
        ,
        paginatedData() {
            const start = this.pageNumber * this.quantity,
                end = start + this.quantity;

            if (this.query) {
                return this.filteredItems.slice(start, end);
            } else {
                return this.items.slice(start, end);
            }
        }
    },
    methods: {
        handleBuyClick(item) {
            this.$emit("onbuy", item)
        },
        nextPage() {
            this.pageNumber++;
        },
        prevPage() {
            this.pageNumber--;
        }
    },
    template: `
        <div><div class="fetured-items-box">
            <product-item @onbuy="handleBuyClick" v-for="entry in paginatedData" :item="entry" :user="user" :key="entry.id"></product-item>
            
            <div class="center" v-if="items.length === 0">
                <h2 class="shopping-cart-forms-text modal_title pink">no matching items. change your query criteria</h2>
            </div>
        </div>
        <div class="more-product">
            <div class="pagination">
                <a href="#" class="button-all button-all_pagination" 
                    :disabled="pageNumber === 0" @click.prevent="prevPage">Previous</a>
                <a href="#" class="button-all button-all_pagination" 
                    :disabled="pageNumber >= pageCount" @click.prevent="nextPage">Next</a>
            </div>
            <div class="button-more-product"><a href="/good" class="button-all">View All</a></div>
        </div></div>`
});

Vue.component("search", {
    data() {
        return {
            searchQuery: ""
        }
    },
    methods: {
        handleSearchClick() {
            this.$emit("search", this.searchQuery)
        }
    },
    template: `
         <div>
             <input class="input-form" type="text" placeholder="Search for Item..." v-model="searchQuery"><button class="button-form" @click.prevent="handleSearchClick"><i
                     class="fas fa-search"></i></button>
         </div>`
});


const app = new Vue({
    el: "#app",
    data: {
        items: [],
        filterValue: "",
        quantityItemOnPage: 8,
        user: "",

        category: "",
        size: [],
        price: "",
        sort: "",

        maxPrice: "",
        totalAmount: 0,
    },
    watch: {
        price() {
            this.getData();
        },
        size() {
            this.getData();
        },
        sort() {
            this.getData();
        },
    },
    mounted() {
        fetch(`${API_URL}/cart/getQuantityCart`, {
            method: "POST"
        })
            .then((response) => response.text())
            .then((data) => {
                this.totalAmount = data;
            });

        fetch(`${API_URL}/good/getMaxPrice`, {
            method: "POST"
        })
            .then((response) => response.text())
            .then((data) => {
                this.maxPrice = data;
                this.price = this.maxPrice;
            });

        fetch(`${API_URL}/auth/getUser`, {
            method: "POST",
        })
            .then((response) => response.json())
            .then((data) => {
                this.user = data;
            });
    },
    methods: {
        handleSearchClick(query) {
            this.filterValue = query;
        },
        handleQuantityItemOnPageClick(size) {
            this.quantityItemOnPage = +size;
        },
        handleBuyClick(id) {
            fetch(`${API_URL}/cart/add?id=${id}`, {
                method: "POST"
            })
                .then((response) => response.text())
                .then((data) => {
                    this.totalAmount = data;
                })
        },
        getCategory(category) {
            this.category = category;
            this.getData();
        },
        getData() {
            fetch(`${API_URL}/good/getOptions`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    category: this.category,
                    size: this.size,
                    sort: this.sort,
                    price: this.price
                })
            })
                .then((response) => response.json())
                .then((data) => {
                    this.items = [];
                    data.forEach((item) => {
                        this.items.push(item);
                    });
                })
        },
    }
});
