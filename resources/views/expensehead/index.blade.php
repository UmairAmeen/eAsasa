@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fa fa-credit-card"></i> Expense Head
            <a accesskey="n" class="btn btn-success pull-right" href="{{ route('expensehead.create') }}"><i
                    class="glyphicon glyphicon-plus"></i> Add Expense Head <br><small>ALT + N</small></a>
        </h1>

    </div>
@endsection

@section('content')
    <div class="col-md-12">
        <a href="{{ url('expenseHeads.xlsx') }}">
            <div class="col-md-2 col-sm-2 box0">
                <div class="box1">
                    <span class="fa fa-arrow-down"></span>
                    <h3>Download Excel</h3>
                    <span>Expense Heads Data</span>
                </div>
            </div>
        </a>

        <a data-toggle="modal" data-target="#uploadExcel">
            <div class="col-md-2 col-sm-2 box0">
                <div class="box1">
                    <span class="fa fa-arrow-up"></span>
                    <h3>Import Excel</h3>
                </div>
            </div>
        </a>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="content-panel">
                @if ($expenses->count())
                    <table class="table table-condensed table-striped datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th class="text-right">OPTIONS</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $count = 1; ?>
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td>{{ $count }}</td>
                                    <td>{{ $expense->name }}</td>
                                    <td class="text-right">
                                        <!-- <a class="btn btn-xs btn-primary" href="{{ route('expensehead.show', $expense->id) }}"><i class="glyphicon glyphicon-eye-open"></i> View</a> -->
                                        @if ($expense->deleteable)
                                            <a class="btn btn-xs btn-warning"
                                                href="{{ route('expensehead.edit', $expense->id) }}"><i
                                                    class="glyphicon glyphicon-edit"></i> Edit</a>
                                            <form action="{{ route('expensehead.destroy', $expense->id) }}" method="POST"
                                                class="no-ajax" style="display: inline;"
                                                onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button type="submit" class="btn btn-xs btn-danger"><i
                                                        class="glyphicon glyphicon-trash"></i> Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                <?php $count++; ?>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <h3 class="text-center alert alert-info">Empty!</h3>
                @endif

            </div>
        </div>
    </div>
    <div aria-hidden="true" aria-labelledby="Upload Excel" role="dialog" data-backdrop="static" id="uploadExcel"
        class="modal fade">
        <div class="modal-dialog" style="width: 90%">
            <div class="modal-content col-md-12">
                <form id="uploadExcelFile" action="{{ url('uploadExpenseHeadsExcel') }}" method="POST"
                    enctype="multipart/form-data" class="no-ajax">
                    <div class="col-md-12">
                        <div class="content-panel">
                            <div id="log"></div>
                            <center>
                                <h1>Please Attach Excel File (xls/xlsx)</h1>
                            </center>
                            <div class="form-group">
                                <input type="file" name="importexcel" class="form-control" accept=".xlsx,.xls">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
                        <center>
                            <button class="btn btn-primary btn-lg" type="submit">Upload</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"
                                aria-label="Close">Close</button>
                        </center>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
