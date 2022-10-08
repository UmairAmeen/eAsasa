           <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" data-backdrop="static"
               id="addproductmodal" class="modal fade">
               <div class="modal-dialog" style="width: 90%;">
                   <div class="modal-content col-md-12 content-panel" style=" min-height: 300px">
                       <div class="col-md-12">
                           <div class="row">
                               <div class="col-md-12">
                                   <form action="{{ route('products.store') }}" method="POST"
                                       id="products_modal_form">
                                       <div id="log" class="___class_+?6___"></div>
                                       <div class="form-group">
                                           <label>Product Name:</label>
                                           <input type="text" name="name[]" class="form-control"
                                               placeholder="Product Name">
                                           <small id="emailHelp" class="form-text text-muted">This is required</small>
                                       </div>

                                       <input type="hidden" value="1" name="product_category[]" class="form-control"
                                           placeholder="Product Category">
                                       <div class="form-group">
                                           <label>Barcode/Item Code:</label>
                                           <input type="text" name="barcode[]" class="form-control"
                                               placeholder="Barcode">
                                       </div>

                                       <div class="form-group">
                                           <label>Brand/Model:</label>
                                           <input type="text" name="brand[]" class="form-control"
                                               placeholder="Product Brand">
                                       </div>

                                       <div class="form-group">
                                           <label>Sale Price:</label>
                                           <input type="text" name="sale_price[]" class="form-control"
                                               placeholder="Sale Price" value="0">
                                       </div>
                                       <div class="form-group">
                                           <label for="min_sale_price">Min Sale Price</label>
                                           <input type="text" class="form-control input-sm" name="min_sale_price[]"
                                               placeholder="Min Sale Price">
                                       </div>
                                       <div class="form-group">
                                           <label for="purchase_price">Cost Price</label>
                                           <input type="text" class="form-control input-sm" name="purchase_price[]"
                                               placeholder="Cost Price">
                                       </div>

                                       <div class="form-group">
                                           <label for="supplier">Supplier</label>
                                           <select name="supplier[]" class="form-control">
                                               @foreach (get_all_suppliers() as $supplier)
                                                   <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                               @endforeach
                                           </select>
                                       </div>


                                       <div class="form-group">
                                           <label>Units:</label>
                                           <select name="unit[]" class="form-control">
                                               @foreach (get_units() as $unit)
                                                   <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                               @endforeach
                                           </select>
                                       </div>


                                       <div class="form-group">
                                           <label>Notify Quantity:</label>
                                           <input type="number" min="0" step="1" name="notify_quantity[]"
                                               class="form-control" placeholder="Notify Quantity" value="0">
                                       </div>

                                       <div class="form-group">
                                           <label for="description">Description</label>
                                           <textarea placeholder="Product Description/Notes" name="description[]"
                                               id="product_desc" class="form-control input-sm"></textarea>
                                       </div>
                                       <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                       <input type="hidden" name="modal_redirection" value="true">
                                       <div class="well well-sm">
                                           <button type="submit" class="btn btn-primary">Create</button>
                                           <button type="button" class="btn btn-link pull-right" data-dismiss="modal"><i
                                                   class="glyphicon glyphicon-backward"></i> Cancel</button>
                                       </div>
                                   </form>

                               </div>
                           </div>
                           <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script> -->
                           <!--custom switch-->
                           <!-- <script src="{{ asset('assets/js/bootstrap-switch.js') }}"></script> -->

                           <script type="text/javascript"></script>
                       </div>
                   </div>
               </div>
           </div>
