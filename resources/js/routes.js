export default [
    { path: '/', component: require('./components/Dashboard.vue').default },

    { path: '/developer', component: require('./components/Developer.vue').default },
    { path: '/users', component: require('./components/users/Users.vue').default },
    { path: '/roles', component: require('./components/users/Roles.vue').default },


    { path: '/locations', component: require('./components/location/List.vue').default },
    { path: '/departments', component: require('./components/location/Department.vue').default },
    { path: '/tables', component: require('./components/location/Table.vue').default },
    { path: '/customer/groups', component: require('./components/customer/CustomerGroup.vue').default },

    { path: '/product/categories', component: require('./components/product/Category.vue').default },
    { path: '/product/brands', component: require('./components/product/Brand.vue').default },
    { path: '/products', component: require('./components/product/Product.vue').default },
    { path: '/food/categories', component: require('./components/food/Category.vue').default },
    { path: '/food/brands', component: require('./components/food/Brand.vue').default },
    { path: '/food', component: require('./components/food/List.vue').default },
    { path: '/modifiers', component: require('./components/food/Modifiers.vue').default },
    { path: '/suppliers', component: require('./components/supplier/List.vue').default },
    { path: '/employee/categories', component: require('./components/employee/Category.vue').default },
    { path: '/employees', component: require('./components/employee/Employee.vue').default },
    { path: '/expenses', component: require('./components/expense/List.vue').default },
    { path: '/expense/categories', component: require('./components/expense/Category.vue').default },
    { path: '/customers', component: require('./components/customer/List.vue').default },
    { path: '/discounts', component: require('./components/discount/List.vue').default },
    { path: '/barcode/form', component: require('./components/product/BarcodeForm.vue').default },
    { path: '/barcode/generate', component: require('./components/product/BarcodeGenerate.vue').default },

    { path: '/product/purchases', component: require('./components/productPurchase/List.vue').default },
    { path: '/product/purchase/add/:id?', component: require('./components/productPurchase/Add.vue').default },
    { path: '/product/purchase/view/:id', component: require('./components/productPurchase/View.vue').default },
    { path: '/product/purchase/return/view/:id', component: require('./components/productPurchase/ReturnView.vue').default },
    { path: '/sale/add', component: require('./components/sale/Add.vue').default },
    { path: '/sales', component: require('./components/sale/List.vue').default },
    { path: '/sale/returns', component: require('./components/sale/ReturnList.vue').default },
    { path: '/purchase/returns', component: require('./components/productPurchase/ReturnList.vue').default },

    { path: '/product/category', component: require('./components/product/Category.vue').default },


    { path: '/accounts', component: require('./components/account/List.vue').default },
    { path: '*', component: require('./components/NotFound.vue').default },

    { path: '/sale/view/:id', component: require('./components/sale/View.vue').default },


    { path: '/table/order/:id', component: require('./components/sale/Table_Order.vue').default },
    { path: '/kot/order/:id', component: require('./components/sale/KOT_Order.vue').default },

    { path: '/sale/restaurant_style_1/:id/:return?', component: require('./components/sale/Print_Super_Market.vue').default },
    { path: '/sale/restaurant_style_2/:id/:return?', component: require('./components/sale/Print_Restaurant_Style_2.vue').default },
    { path: '/sale/restaurant_style_3/:id/:return?', component: require('./components/sale/Print_Restaurant_Style_3.vue').default },
    { path: '/sale/restaurant_style_4/:id/:return?', component: require('./components/sale/Print_Restaurant_Style_4.vue').default },
    { path: '/sale/restaurant_style_5/:id/:return?', component: require('./components/sale/Print_Restaurant_Style_5.vue').default },
    { path: '/sale/bakery_style_1/:id/:return?', component: require('./components/sale/Print_Bakery.vue').default },
    { path: '/sale/return/:id', component: require('./components/sale/Return.vue').default },
    { path: '/purchase/order/print/:id', component: require('./components/productPurchase/Print.vue').default },
    { path: '/purchase/return/:id', component: require('./components/productPurchase/Return.vue').default },

    { path: '/employee/salary/transactions', component: require('./components/employee/EmployeeSalaryTransaction.vue').default },

    { path: '/customer/points/view/:id', component: require('./components/customer/Point.vue').default },
    { path: '/sale/token/display', component: require('./components/sale/Token.vue').default },
    { path: '/customer/sales/:id', component: require('./components/customer/SaleList.vue').default },
    { path: '/account/transactions', component: require('./components/account/Transactions.vue').default },
     { path: '/sale/running', component: require('./components/sale/Running.vue').default },

    { path: '/settings', component: require('./components/Setting.vue').default },

    { path: '/report/kitchen/order', component: require('./components/report/kitchenOrder.vue').default },
    { path: '/report/product/consumption', component: require('./components/report/productConsumption.vue').default },
    { path: '/report/expense/category', component: require('./components/report/ExpenseCategory.vue').default },
    { path: '/report/consolidated', component: require('./components/report/Consolidated.vue').default },
    { path: '/report/product/sale', component: require('./components/report/ProductSale.vue').default },
    { path: '/report/food/category', component: require('./components/report/FoodCategory.vue').default },
    { path: '/report/account', component: require('./components/report/Account.vue').default },
    { path: '/report/product/purchase', component: require('./components/report/ProductPurchase.vue').default },
    { path: '/report/gst', component: require('./components/report/GST.vue').default },
    { path: '/report/payment_method', component: require('./components/report/Payment_Method.vue').default },
    { path: '/report/supplier/transactions', component: require('./components/report/supplierTransactions.vue').default },
    { path: '/report/supplier/stock', component: require('./components/report/supplierStock.vue').default },
    { path: '/report/supplier/stock', component: require('./components/report/supplierStock.vue').default },
    { path: '/report/supplier/sales', component: require('./components/report/supplierSale.vue').default },
    { path: '/pos/list', component: require('./components/POSList.vue').default },
    { path: '/pos', component: require('./components/POS.vue').default },
    { path: '/tab/pos', component: require('./components/TAB_POS.vue').default },
];
