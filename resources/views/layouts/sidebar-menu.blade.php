<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      {{--<li class="nav-item">
        <router-link to="/dashboard" class="nav-link">
          <i class="nav-icon fas fa-tachometer-alt blue"></i>
          <p>
            Dashboard
          </p>
        </router-link>
      </li>--}}








      <li class="nav-item has-treeview">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-poll green"></i>
        <p>
          Sales
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">

        <li class="nav-item">
          <router-link to="/sales" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Sales List
            </p>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/customers" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Customers
            </p>
          </router-link>
        </li>
        <!--<li class="nav-item">
          <router-link to="/customer/groups" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Customer Groups
            </p>
          </router-link>
        </li>-->
        <!--<li class="nav-item">
          <router-link to="/sale/returns" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Return List
            </p>
          </router-link>
        </li>-->
        <!-- <li class="nav-item">
          <router-link to="/sale/running" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Running Orders
            </p>
          </router-link>
        </li> -->
        <li class="nav-item kfc">
          <router-link to="/sale/token/display" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Token Display
            </p>
          </router-link>
        </li>

      </ul>
    </li>

    <li class="nav-item has-treeview">
    <a href="#" class="nav-link">
      <i class="nav-icon fas fa-cubes red"></i>
      <p>
        Food Menu
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">

      <li class="nav-item">
        <router-link to="/food" class="nav-link">
          <i class="nav-icon fas fa-list-ol green"></i>
          <p>
            Menu List
          </p>
        </router-link>
      </li>
      <li class="nav-item">
        <router-link to="/modifiers" class="nav-link">
          <i class="nav-icon fas fa-list-ol green"></i>
          <p>
            Modifiers
          </p>
        </router-link>
      </li>
      <li class="nav-item">
        <router-link to="/food/categories" class="nav-link">
          <i class="nav-icon fas fa-list-ol green"></i>
          <p>
            Categories
          </p>
        </router-link>
      </li>
      <li class="nav-item">
        <router-link to="/food/brands" class="nav-link">
          <i class="nav-icon fas fa-list-ol green"></i>
          <p>
            Brands
          </p>
        </router-link>
      </li>
    {{--  <li class="nav-item">
        <router-link to="/product/units" class="nav-link">
          <i class="nav-icon fas fa-list-ol green"></i>
          <p>
            Units
          </p>
        </router-link>
      </li>--}}

    </ul>
  </li>

        <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-cubes red"></i>
          <p>
            Products
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">

          <li class="nav-item">
            <router-link to="/products" class="nav-link">
              <i class="nav-icon fas fa-list-ol green"></i>
              <p>
                Product List
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/product/categories" class="nav-link">
              <i class="nav-icon fas fa-list-ol green"></i>
              <p>
                Categories
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/product/brands" class="nav-link">
              <i class="nav-icon fas fa-list-ol green"></i>
              <p>
                Brands
              </p>
            </router-link>
          </li>
        {{--  <li class="nav-item">
            <router-link to="/product/units" class="nav-link">
              <i class="nav-icon fas fa-list-ol green"></i>
              <p>
                Units
              </p>
            </router-link>
          </li>--}}

        </ul>
      </li>

    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-compass yellow"></i>
          <p>
            Locations
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">

          <li class="nav-item">
            <router-link to="/tables" class="nav-link">
              <i class="nav-icon fas fa-list-ol green"></i>
              <p>
                Tables
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/departments" class="nav-link">
              <i class="nav-icon fas fa-list-ol green"></i>
              <p>
                Kitchens
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/locations" class="nav-link">
              <i class="nav-icon fas fa-list-ol green"></i>
              <p>
                Branches
              </p>
            </router-link>
          </li>

        </ul>
      </li>

        <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-cog green"></i>
          <p>
            Accounts
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">

          <li class="nav-item">
            <router-link to="/accounts" class="nav-link">
              <i class="nav-icon fas fa-list-ol green"></i>
              <p>
                Accounts
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/account/transactions" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Transactions
              </p>
            </router-link>
          </li>
        </ul>
      </li>


      <li class="nav-item has-treeview">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-cog green"></i>
        <p>
          Purchase
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">


        <li class="nav-item">
          <router-link to="/product/purchases" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Purchase Products
            </p>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/suppliers" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Suppliers
            </p>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/purchase/returns" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Return List
            </p>
          </router-link>
        </li>

      </ul>
    </li>


      <li class="nav-item has-treeview">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-cog green"></i>
        <p>
          Employees
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">

        <li class="nav-item">
          <router-link to="/employees" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Employee List
            </p>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/employee/categories" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Employee Categories
            </p>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/employee/salary/transactions" class="nav-link">
            <i class="nav-icon fas fa-list-ol green"></i>
            <p>
              Salary Transactions
            </p>
          </router-link>
        </li>


      </ul>
    </li>






    <li class="nav-item has-treeview">
    <a href="#" class="nav-link">
      <i class="nav-icon fas fa-cog green"></i>
      <p>
        Expense
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">

      <li class="nav-item">
        <router-link to="/expenses" class="nav-link">
          <i class="nav-icon fas fa-list-ol green"></i>
          <p>
            Expense List
          </p>
        </router-link>
      </li>
      <li class="nav-item">
        <router-link to="/expense/categories" class="nav-link">
          <i class="nav-icon fas fa-list-ol green"></i>
          <p>
            Expense Categories
          </p>
        </router-link>
      </li>
    </ul>
  </li>


    <!--<li class="nav-item">
      <router-link to="/discounts" class="nav-link">
        <i class="fas fa-cog nav-icon blue"></i>
        <p>Discounts</p>
      </router-link>
    </li>-->
    <li class="nav-item">
      <router-link to="/users" class="nav-link">
        <i class="fa fa-users nav-icon blue"></i>
        <p>Users</p>
      </router-link>
    </li>






      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-cog green"></i>
          <p>
            Reports
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">

          <li class="nav-item">
            <router-link to="/report/consolidated" class="nav-link">
              <i class="nav-icon fas fa-list-ol green"></i>
              <p>
                Consolidated Report
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/report/product/sale" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Item Sale Report
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/report/food/category" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Food Category Report
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/report/account" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Account Report
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/report/product/purchase" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Product Purchase Report
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/report/product/consumption" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Product Consumption Report
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/report/payment_method" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Payment-Method Report
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/report/kitchen/order" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Kitchen Order Report
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/report/gst" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                GST Consolidated Report
              </p>
            </router-link>
          </li>
          {{-- <li class="nav-item">
            <router-link to="/report/gst/sale/bill" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                GST Sales Wise Report
              </p>
            </router-link>
          </li> --}}
          <li class="nav-item">
            <router-link to="/report/supplier/transactions" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Supplier Wise Transactions
              </p>
            </router-link>
          </li>
          <!--<li class="nav-item">
            <router-link to="/report/supplier/stock" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Supplier-wise Stock
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/report/supplier/sales" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Supplier-wise Sales
              </p>
            </router-link>
          </li> -->
          <li class="nav-item">
            <router-link to="/report/expense/category" class="nav-link">
              <i class="nav-icon fas fa-tags green"></i>
              <p>
                Expense Category-wise Report
              </p>
            </router-link>
          </li>


        </ul>
      </li>

      <li class="nav-item">
        <router-link to="/settings" class="nav-link">
          <i class="fa fa-cog nav-icon green"></i>
          <p>Settings</p>
        </router-link>
      </li>











      <li class="nav-item">
        <a href="#" class="nav-link" onclick="event.preventDefault();
          document.getElementById('logout-form').submit();">
          <i class="nav-icon fas fa-power-off red"></i>
          <p>
            {{ __('Logout') }}
          </p>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </li>
    </ul>
  </nav>
