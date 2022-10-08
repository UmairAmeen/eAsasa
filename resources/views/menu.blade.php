                  <li class="mt sub-menu">
                      <a id="dashboard_font" @if (isset($dashboard)) class="active" @endif href="{{ url('/') }}"
                          accesskey="d">
                          <i class=" fas fa-tachometer-alt"></i>
                          Dashboard
                      </a>
                      {{-- <i class="fas fa-tachometer-alt" style="font-size: 20px"></i>
                      <span>Dashboard <br><small class="hidden-xs hidden-sm">ALT + D</small></span> --}}
                  </li>
                  <div id="products_parent" onclick="makeVisible(this)" class="unselectable">
                      <li>
                          <a class="font-color" id="warehouse_font">
                              <i class="fa fa-archive"></i>
                              Products<br>
                          </a>
                      </li>
                      <div id="products_submenu" class="selectalltohide">
                          @if (package('products') && is_allowed('product-list'))
                              <li>
                                  <a @if (isset($product_menu)) class="active" @endif
                                      href="{{ url('products') }}" accesskey="p">
                                      <i class="fa fa-archive"></i>
                                      <span>Products<br><small class="hidden-xs hidden-sm">ALT + P</small></span>
                                  </a>
                              </li>
                          <li>
                              <a @if (isset($product_category_menu)) class="active" @endif
                                  href="{{ url('product_categories') }}">
                                  <i class="fas fa-object-group"></i>
                                  <span>Product Category</span>
                              </a>
                          </li>
                      @endif
                          @if (package('products') && is_allowed('product-list'))
                              <li>
                                  <a @if (isset($units_menu)) class="active" @endif href="{{ url('units') }}"
                                      accesskey="u">
                                      <i class="fas fa-balance-scale"></i>
                                      <span>Unit<br><small class="hidden-xs hidden-sm">ALT + U</small></span>
                                  </a>
                              </li>
                          @endif
                      </div>
                  </div>
                  <div id="customer_supplier_parent" onclick="makeVisible(this)" class="unselectable">
                      <li>
                          <a class="font-color" id="customer_font">
                              <i class="fa fa-user"></i>
                              Customers/Suppliers<br>
                          </a>
                      </li>
                      <div id="customer_supplier" class="selectalltohide">
                          @if (package('customer') && is_allowed('customer-list'))
                              <li>
                                  <a href="{{ url('customers') }}" accesskey="c">
                                      <i class="fa fa-user"></i>
                                      <span>Customers<br><small class="hidden-xs hidden-sm">ALT + C</small></span>
                                  </a>
                              </li>
                          @endif
                          @if (package('supplier') && is_allowed('supplier-list'))
                              <li>
                                  <a href="{{ url('suppliers') }}" accesskey="s">
                                      <i class="fa fa-truck"></i>
                                      <span>Suppliers<br><small class="hidden-xs hidden-sm">ALT + S</small></span>
                                  </a>
                              </li>
                          @endif
                          @if (package('purchase') && is_allowed('report-product_record_supplier'))
                              <li>
                                  <a href="{{ url('supplier_price_records') }}">
                                      <i class="fa fa-file-invoice-dollar"></i>
                                      <span>Supplier Price Record</span>
                                  </a>
                              </li>
                          @endif
                      </div>
                  </div>
                  @if (package('sales_person') && is_allowed('sales-person'))
                  <li>
                      <a @if (isset($salesPerson_menu)) class="active" @endif href="{{ url('salesPerson') }}">
                          <i class="fa fa-handshake"></i>
                          <span style="font-size: 14px">Sales Persons<br>
                              {{-- <small class="hidden-xs hidden-sm">ALT + E</small> --}}
                          </span>
                        </a>
                    </li>
                  @endif
                  {{-- <div id="warehouse_parent" onclick="makeVisible(this)" class="unselectable">
                      <li>
                          <a class="font-color" id="warehouse_font">
                              <i class="fa fa-warehouse"></i>
                              Warehouses<br>
                          </a>
                      </li>
                      <div id="warehouse_submenu" class="selectalltohide"> --}}
                          @if (package('customer') && is_allowed('customer-list'))
                              <li>
                                  <a @if (isset($warehouse_menu)) class="active" @endif
                                      href="{{ url('warehouses') }}" accesskey="w">
                                      <i class="fas fa-warehouse"></i>
                                      <span>Warehouses<br><small class="hidden-xs hidden-sm">ALT + W</small></span>
                                  </a>
                              </li>
                          @endif
                      {{-- </div>
                    </div>                       --}}

                  <div id="purchase_sale_parent" onclick="makeVisible(this)" class="unselectable">
                      <li>
                          <a class="font-color" id="purchase_font">
                              <i class=" fa fa-credit-card"></i>
                              Purchases/Sales<br>
                          </a>
                      </li>
                      <div id="purchase_sale" class="selectalltohide">
                          @if (package('purchase') && is_allowed('purchase-list'))
                              <li>
                                  <a @if (isset($purchase_menu)) class="active" @endif
                                      href="{{ url('purchases') }}" accesskey="i">
                                      <i class="fa fa-credit-card"></i>
                                      <span>Purchases<br><small class="hidden-xs hidden-sm">ALT + I</small></span>
                                  </a>
                              </li>
                          @endif


                          @if (package('sales') && is_allowed('sale-list'))
                              <li>
                                  <a @if (isset($sales_menu)) class="active" @endif href="{{ url('sales') }}"
                                      accesskey="k">
                                      <i class="fas fa-receipt"></i>
                                      <span>Direct Sales Invoice<br><small class="hidden-xs hidden-sm">ALT +
                                              K</small></span>
                                  </a>
                              </li>
                          @endif
                          @if (package('sales') && is_allowed('sale-list'))
                              <li>
                                  <a @if (isset($sales_order_menu)) class="active" @endif
                                      href="{{ url('sale_orders') }}" accesskey="o">
                                      <i class="fas fa-truck-loading"></i>
                                      <span>Sales Order<br><small class="hidden-xs hidden-sm">ALT + O</small></span>
                                  </a>
                              </li>
                          @endif
                          @if(package('deliverychallans'))
                          <li >
                              <a @if(isset($deliverychallans)) class="active" @endif href="{{url('deliverychallans')}}">
                                  <i class="fa fa-clipboard"></i>
                                  <span> Delivery Challans </span>
                              </a>
                          </li>
                          @endif

                      </div>
                  </div>

                  <div id="transaction_parent" onclick="makeVisible(this)" class="unselectable">
                      <li>
                          <a class="font-color" id="stock_font">
                              <i class=" fa fa-money-check"></i>
                              Transactions<br>
                          </a>
                      </li>
                      <div id="transactions_submenu" class="selectalltohide">
                          @if (package('transaction') && is_allowed('transaction-list'))
                              <li>
                                  <a @if (isset($transaction_menu)) class="active" @endif
                                      href="{{ url('transactions') }}" accesskey="t">
                                      <i class="fa fa-database"></i>
                                      <span>Transactions<br><small class="hidden-xs hidden-sm">ALT + T</small></span>
                                  </a>
                              </li>
                          @endif
                          @if (package('expensehead'))
                              <li>
                                  <a @if (isset($expensehead_menu)) class="active" @endif
                                      href="{{ url('expensehead') }}">
                                      <i class="fa fa-credit-card"></i>
                                      <span> Head Expense </span>
                                  </a>
                              </li>
                          @endif
                      </div>
                  </div>

                      {{-- <div id="stock_parent" onclick="makeVisible(this)" class="unselectable">
                          <li>
                              <a class="font-color" id="stock_font">
                                  <i class=" fa fa-database"></i>
                                  Stocks<br>
                              </a>
                          </li>
                      </div> --}}

                    @if (package('stock_adjustments') && is_allowed('stocks-list'))
                    <li>
                        <a @if (isset($stock_adjustments)) class="active" @endif href="{{ url('stock') }}" accesskey="e">
                            <i class="fa fa-bars  "></i>
                            <span>Stocks<br>
                                <small class="hidden-xs hidden-sm">ALT + E</small>
                            </span>
                          </a>
                      </li>
                    @endif


                      <div id="settings_notifications_parent" onclick="makeVisible(this)" class="unselectable">
                          <li>
                              <span>
                                  <a class="font-color" id="stock_font">
                                      <i class=" fas fa-sliders-h"></i>
                                      Settings/Notify
                                  </a>
                              </span>
                          </li>
                          <div id="settings_notifications" class="selectalltohide">
                              @if (package('notification') && is_admin())
                                  <li>
                                      <a @if (isset($appointment_menu)) class="active" @endif
                                          href="{{ url('appointment_calendars') }}" accesskey="n">
                                          <i class="fa fa-calendar"></i>
                                          <span>Calendar &amp; Notification<br><small class="hidden-xs hidden-sm">ALT +
                                                  N</small></span>
                                      </a>
                                  </li>
                              @endif
                              @if (package('settings') && is_admin())
                                  <li>
                                      <a @if (isset($settings_menu)) class="active" @endif
                                          href="{{ url('settings') }}">
                                          <i class="fas fa-sliders-h"></i>
                                          <span>Settings</span>
                                      </a>
                                  </li>
                              @endif
                              @if (is_admin())
                                  <li>
                                      <a @if (isset($user_menu)) class="active" @endif
                                          href="{{ url('users') }}">
                                          <i class="fa fa-user-circle"></i>
                                          <span>Users</span>
                                      </a>
                                  </li>
                              @endif
                              @if (is_admin())
                                <li>
                                    <a href="{{ url('/bank_accounts') }}">
                                        <i class=" fas fa-file-invoice-dollar"></i>
                                        Bank account
                                    </a>
                                </li>
                                @endif
            
                              @if (is_admin())
                                  <li>
                                      <a @if (isset($role_menu)) class="active" @endif
                                          href="{{ url('roles') }}">
                                          <i class="fa fa-users"></i>
                                          <span>Roles</span>
                                      </a>
                                  </li>
                              @endif
                              @if (is_admin())
                                    <li>
                                      <a @if (isset($role_menu)) class="active" @endif
                                          href="{{ url('promotion') }}">
                                          <i class="fa fa-bullhorn"></i>
                                          <span>Promotion</span>
                                      </a>
                                  </li>
                                @endif
                              @if (is_allowed('backup'))
                                  <li>
                                      <a @if (isset($backup_menu)) class="active" @endif
                                          href="{{ url('backup') }}">
                                          <i class="fas fa-save  "></i>
                                          <span>Backup</span>
                                      </a>
                                  </li>
                              @endif
                          </div>
                      </div>

                  @if (env('HR_ENABLE',false) && \Nwidart\Modules\Facades\Module::collections()->has('HumanResource') &&  is_allowed('access-hr'))
                      <li class="mt sub-menu">
                          <a id="dashboard_font" href="{{ url('/hr') }}" accesskey="d">
                              <i class=" fas fa-users"></i>
                              Human Resources
                          </a>
                      </li>
                  @endif

                      <!--  @if (package('invoice')) <li >
                      <a @if (isset($invoice_menu)) class="active" @endif href="{{ url('stock') }}">
                          <i class="fa fa-file-text-o  "></i>
                          <span>Invoices</span>
                      </a>
                  </li>
                  @endif -->
                      <!--  @if (package('manufacture')) <li >
                      <a @if (isset($manufacture_menu)) class="active" @endif href="{{ url('stock') }}">
                          <i class="fa fa-server  "></i>
                          <span>Manufacture Product</span>
                      </a>
                  </li>
                  @endif -->
                      @if (package('transaction') && is_allowed('transaction-list') && 0 == 1)
                          <li>
                              <a @if (isset($cheque_managers)) class="active" @endif accesskey="7"
                                  href="{{ url('cheque_managers') }}">
                                  <i class="fa fa-cc  "></i>
                                  <span>Cheque Manager<br><small class="hidden-xs hidden-sm">ALT + 7</small></span>
                              </a>
                          </li>
                      @endif

                      @if (package('refund') && 0 == 1)
                          <li>
                              <a @if (isset($refund_menu)) class="active" @endif href="{{ url('refunds') }}">
                                  <i class="fas fa-retweet  "></i>
                                  <span>Refund</span>
                              </a>
                          </li>
                      @endif

                      @if (package('report') && is_allowed('report-list'))
                          <li>
                              <a @if (isset($reports_menu)) class="active" @endif href="{{ url('reports') }}" id="report_font">
                                  <i class=" fas fa-chart-pie"></i> Reports</a>
                              {{-- <i class="fas fa-chart-pie" aria-hidden="true" accesskey="r"></i> --}}
                              {{-- <br><small class="hidden-xs hidden-sm">ALT + R</small> --}}
                          </li>
                      @endif
