<style>
    .tr-height {
        line-height: 24px;
    }

    .tr-font {
        font-size: 2em;
        text-decoration-color: #392A10;
    }

    /* .td-bottom { border-bottom: 1px solid black; } */
    .center-text {
        text-align: center;
    }

</style>

<table width="100%" border="0">
    <tr>
        <td>
            <table width="100%" border="0">
                <tr class="tr-height">
                    <td width="12%">Date: </td>
                    <td width="28%">{{ date('d-m-Y', strtotime($challan->date)) }}</td>
                    <td width="15%"></td>
                    <td width="15%"></td>
                    <td width="12%">Delivery No:</td>
                    <td width="18%">{{ $challan->id }}</td>
                </tr>
                <tr class="tr-height">
                    <td>Customer: </td>
                    <td><b>{{ $customer->name }}</b></td>
                    <td class="center-text">Mobile:</td>
                    <td><b>{{ $customer->phone }}</b></td>
                    <td width="12%">Order No:</td>
                    <td width="18%">{{ str_pad($challan->order_no, 6, '0', STR_PAD_LEFT) }}</td>

                </tr>
                <tr class="tr-height">
                    <td>Address: </td>
                    <td colspan="3" rowspan="2">{{ !empty($challan->address) ? $challan->address : $customer->address }}
                    </td>
                    <td width="12%">Rep By:</td>
                    <td width="18%">{{ $challan->rep_by }}</td>
                    {{-- <td  width="18%"></td> --}}
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2"></td>
                </tr>
            </table>

        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
</table>