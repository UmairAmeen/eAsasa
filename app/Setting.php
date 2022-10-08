<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['module', 'name', 'key', 'value', 'defaultValue', 'type', 'updated_by'];

    const LABEL      = 'label';
    const TEXTBOX    = 'text';
    const PHONE      = 'phone';
    const NUMBER     = 'number';
    const SELECT     = 'select';
    const SELECT2    = 'select2';
    const CHECKBOX   = 'checkbox';
    const TEXTAREA   = 'textarea';
    const DISABLED   = 'disabled';
    const PASSWORD   = 'password';

    const MIN_ITEMS_PER_INVOICE = 1;
    const MAX_ITEMS_PER_INVOICE = 20;

    const MIN_TAX_PERCENT = 0;
    const MAX_TAX_PERCENT = 50;


    public static $ModuleList = [
        'profile' => 'Company Profile',
        'misc' => 'Miscellaneous',
        'sales' => "Sales",
        'accounts' => "Accounts",
        'products' =>"Products",
        'barcode' => 'Product Barcode',
        'fbr' => 'FBR Invoice',
    ];

    public static $optionalProductItems = [
        'itemcode' => 'Item Code',
        'size' => 'Size',
        'color' => 'Color',
        'pattern' => 'Pattern',
        'length' => 'Length',
        'width' => 'Width',
        'height' => 'Height',
        'description' => 'Description',
        'category' => 'Category',
    ];
    public static $termsOnBack = [
        '0' => 'Default',
        '1' => 'Each Page',
        '2' => 'First Page',
        '3' => 'Last Page',
    ];
    public static $productFieldsForInvoice = [
        'name' => 'Name',
        'size' => 'Size',
        'color' => 'Color',
        'pattern' => 'Pattern',
        'translation' => 'Translation',
        'brand' => 'Brand',
        'itemcode' => 'Item Code',
        'description' => 'Description',
        'category' => 'Category',
    ];
    public static $barcodeFields = [
        'company' => 'Company Name',
        'barcode' => 'Barcode Text',
        'name' => 'Name',
        'size' => 'Size',
        'price' => 'Price',
    ];
    public static $dateFormats = [
        'd-m-Y' => '31-12-2020',
        'm-d-Y' => '12-31-2020',
        'Y-m-d' => '2020-12-31',
        'd/m/Y' => '31/12/2020',
        'm/d/Y' => '12/31/2020',
        'Y/m/d' => '2020/12/31',
        'd-M-Y' => '31-Dec-2021',
        'M-d-Y' => 'Dec-31-2021',
        'd/M/Y' => '31/Dec/2021',
        'M/d/Y' => 'Dec/31/2021',
        'jS M, Y' => '31st Dec, 2021',
        'M jS, Y' => 'Dec 31st, 2021',
        'd F, Y' => '31 December, 2020',
        'F d, Y' => 'December 31, 2020',
    ];

    public static function GetDefaultValues()
    {
        $settings = [
            'profile' => [
                'company' => [
                    "type" => self::TEXTBOX,
                    "title" => 'Company Name',
                    "default" => 'Your Company Name',
                ],
                'address' => [
                    "type" => self::TEXTBOX,
                    "title" => 'Address',
                    "default" => 'Your Company Business Address',
                ],
                "phone" => [
                    "type" => self::PHONE,
                    "title" => 'Phone/Mobile',
                    "default" => '+92-345-4777487',
                ],
                "notification_phone" => [
                    "type" => self::PHONE,
                    "title" => 'Notification Contact Number',
                    "default" => '+92-345-4777487',
                ],
                "notification_line" => [
                    "type" => self::TEXTBOX,
                    "title" => 'Notification Line ID',
                    "default" => '',
                ],
                'sms_enable' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Enable SMS',
                    "default" => 0,
                    "tooltip" => "To Enable/Disable SMS"
                ],
                "sms_url" => [
                    "type" => self::TEXTBOX,
                    "title" => 'SMS URL',
                    "default" => '',
                ],
                "sms_user_name" => [
                    "type" => self::TEXTBOX,
                    "title" => 'SMS User Name',
                    "default" => '',
                ],
                "sms_mask" => [
                    "type" => self::TEXTBOX,
                    "title" => 'SMS MASK',
                    "default" => '',
                ],
                "sms_password" => [
                    "type" => self::PASSWORD,
                    "title" => 'SMS Password',
                    "default" => '',
                ],
                "sms_promotional" => [
                    "type" => self::TEXTAREA,
                    "title" => 'Promotional SMS',
                    "default" => '',
                ],
            ],
            'sales' => [
                'is_image_enable_in_sale_invoice' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Show Image in Sales Invoice',
                    "default" => 0,
                    "tooltip" => "To Enable/Disable QR Code on Invoice"
                ],
                'allow_sales_on_negative_inventory' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Allow Direct Sales on Negative Inventory',
                    "default" => 1,
                    "tooltip" => "Skip Stock Check For Direct Sales Invoice",
                ],
                'is_image_enable_in_purchase_order' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Show Image in Purchase Order',
                    "default" => 0,
                    "tooltip" => "To Enable/Disable QR Code on Invoice"
                ],
                'is_image_enable_on_delivery_challan' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Show Image on Dilivery Challan',
                    "default" => 0,
                    "tooltip" => "To Enable/Disable Image on Dilivery Challan"
                ],
                'is_bank_enable_in_direct_sale_invoice' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Show Bank Field in Direct Sale Invoice',
                    "default" => 0,
                    "tooltip" => "To Enable/Disable Bank Field In Direct Sale Invoice"
                ],
                'is_invoice_qr_enable' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Show QR Code on Invoice',
                    "default" => 0,
                    "tooltip" => "To Enable/Disable QR Code on Invoice"
                ],
                'show_empty_rows' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Show Blank Rows on Invoice',
                    "default" => 0,
                    "tooltip" => "Fill space on invoice with empty rows"
                ],
                'use_stock' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Use Product Purchase Batch while Sale Invoice',
                    "default" => 0,
                    "tooltip" => "To Enable/Disable Negative Stock During Sale",
                ],
                'use_customer_pricing' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Use Customer Pricing for each product sale',
                    "default" => 0,
                    "tooltip" => "Get price of product from sale hisotry",
                ],
                "show_customer_previous_balance" => [
                    "type" => self::CHECKBOX,
                    "title" => 'Customer Balance on Invoice',
                    "default" => 0,
                    "tooltip" => "Display Customer Previous Balance on Invoice",
                ],
                'is_sale_invoice' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Sale Tax on Sale Invoice',
                    "default" => 0,
                    "tooltip" => "Apply Sales Tax on each Sales Invoice",
                ],
                'enable_post_order' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Enable Post by Default in Sale order',
                    "default" => 0,
                    "tooltip" => "Apply Post Order option in Sale order",
                ],
                'enable_units_invoice' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Enable Units in Invoice',
                    "default" => 0,
                    "tooltip" => "Apply product units in invoices",
                ],
                'show_amount_in_words' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Show Amount In Words on Invoice',
                    "default" => 0,
                    "tooltip" => "To Enable/Disable Amount In Words on Invoice",
                ],
                'tax_percentage' => [
                    "type" => self::NUMBER,
                    "title" => 'Sales Tax Percentage',
                    "default" => 16,
                    "min" => self::MIN_TAX_PERCENT,
                    "max" => self::MAX_TAX_PERCENT,
                    "tooltip" => "Sales Tax Percentage if Sale Tax is enabled",
                ],
                'items_per_page' => [
                    "type" => self::NUMBER,
                    "title" => 'Items Per Page on Invoice',
                    "default" => 15,
                    "min" => self::MIN_ITEMS_PER_INVOICE,
                    "max" => self::MAX_ITEMS_PER_INVOICE,
                    "tooltip" => "Add number of rows on Invoice each Page",
                ],
                'custom_items' => [
                    "type" => self::TEXTBOX,
                    "title" => 'Type the fields you want to use as input in Sale Invoice',
                    "default" => '',
                    'class' => 'form-control'
                ],
                'terms_on_back' => [
                    "type" => self::SELECT,
                    "title" => 'Terms and Conditions on back of Invoice',
                    "default" => '',
                    "options" => self::$termsOnBack,
                    "tooltip" => "Add Terms and Conditions back side of Invoice",
                ],
                'invoice_terms' => [
                    "type" => self::TEXTAREA,
                    "title" => 'Invoice Terms and Conditions',
                    "default" => '',
                    "tooltip" => "Add Terms and Conditions for Sales Invoice",
                ],
            ],
            'misc' => [
                'version' => [
                    "type" => self::LABEL,
                    "title" => 'Version',
                    "default" => '1.0.0',
                    "tooltip" => "Application's Current Version",
                ],
                'item_combine' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Combine Same Item in Invoice',
                    "default" => 1,
                    "tooltip" => "Combine All the same order item in invoice",
                ],
                'eng_urdu' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Invoice Print In Urdu',
                    "default" => 0,
                    "tooltip" => "Invoice Print In Urdu and English",
                ],
                'date_format' => [
                    "type" => self::SELECT,
                    "title" => 'Set Date Format',
                    "default" => 'd-m-Y',
                    "options" => self::$dateFormats,
                    'class' => 'form-control'
                ],
                'pending_sales' => [
                    "type" => self::NUMBER,
                    "title" => 'Number of days for pending Orders to be Delivered',
                    "default" => 5,
                    "min" => 1,
                    "max" => 100,
                    "tooltip" => "Show Pending sales with delivery date on Dashboard",
                ],
                'content_position' => [
                    "type" => self::NUMBER,
                    "title" => 'Adjust Content Position',
                    "default" => 15,
                    "min" => 1,
                    "max" => 100,
                    "tooltip" => "clone same barcode for sticker printing",
                ],
                'client_font_size' => [
                    "type" => self::NUMBER,
                    "title" => 'Invoice Client Details font size',
                    "default" => 9,
                    "min" => 4,
                    "max" => 30,
                    "tooltip" => "to adjust price text font size",
                ],
                'total_font_size' => [
                    "type" => self::NUMBER,
                    "title" => 'Invoice Total Details font size',
                    "default" => 9,
                    "min" => 4,
                    "max" => 30,
                    "tooltip" => "to adjust price text font size",
                ],
                'footer_position' => [
                    "type" => self::NUMBER,
                    "title" => 'Number of barcode copie(s)',
                    "default" => 90,
                    "min" => 1,
                    "max" => 100,
                    "tooltip" => "clone same barcode for sticker printing",
                ],
                'custom_header_footer' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Use Custom Header Footer for Print',
                    "default" => 0,
                    "tooltip" => "Update Custom Header Footer for Print",
                ],
                'invoice_header' => [
                    "type" => self::TEXTAREA,
                    "title" => 'Header Settings',
                    "default" => '',
                ],
                'invoice_footer' => [
                    "type" => self::TEXTAREA,
                    "title" => 'Footer Settings',
                    "default" => '',
                ],
            ],
            'accounts' => [
                'fiscal_start' => [
                    "type" => self::TEXTBOX,
                    "title" => 'Fiscal Year Start',
                    "default" => '01-01',
                    'class' => 'form-control readonly dm_picker',
                    'id' => 'accountsFiscalStart',
                ],
                'fiscal_end' => [
                    "type" => self::TEXTBOX,
                    "title" => 'Fiscal Year End',
                    "default" => '31-12',
                    'class' => 'form-control readonly',
                    'id' => 'accountsFiscalEnd',
                ],
                'transaction_search_filter' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Enable Search Filter For Listing',
                    "default" => 0,
                    "tooltip" => "Display Search Filters for Transactions Listing",
                ]
            ],
            'products' => [
                'enable_advance_fields' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Display product advance form',
                    "default" => 1,
                    "tooltip" => "Dsiplay Supplier and Purchase Price detail",
                ],
                'is_image_enable' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Use Product Image',
                    "default" => 1,
                    "tooltip" => "Upload and Display product image",
                ],
                'ignore_stock_verification' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Allow Negative Inventory',
                    "default" => 0,
                    "tooltip" => "Skip Stock Check to allow negative stock in inventory",
                ],
                'seprate_prod_fields' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Allow Seprate Product Fields',
                    "default" => 0,
                    "tooltip" => "Dispaly Product Fields Separately on Invoice",
                ],
                'optional_items' => [
                    "type" => self::SELECT2,
                    "title" => 'Add optional items in Products',
                    "default" => '',
                    "options" => self::$optionalProductItems,
                    'class' => 'form-control select2'
                ],
                'invoice_fields' => [
                    "type" => self::SELECT2,
                    "title" => 'Fields that will display on invoice',
                    "default" => 'name',
                    "options" => self::$productFieldsForInvoice,
                    'class' => 'form-control select2',
                    "tooltip" => "Add multiple fields that will be shown on invoice as item's description",
                ],
            ],
            'barcode' => [
                'is_enable' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Use Barcode for each product',
                    "default" => 0,
                    "tooltip" => "Use Unique Code for each item/product",
                ],
                'no_of_copies' => [
                    "type" => self::NUMBER,
                    "title" => 'Number of barcode copie(s)',
                    "default" => 1,
                    "min" => 1,
                    "max" => 2,
                    "tooltip" => "clone same barcode for sticker printing",
                ],
                'line_height' => [
                    "type" => self::NUMBER,
                    "title" => 'Height of item\'s text',
                    "default" => 16,
                    "min" => 4,
                    "max" => 32,
                    "tooltip" => "to adjust height and spacing adjust height field",
                ],
                'price_font_size' => [
                    "type" => self::NUMBER,
                    "title" => 'Price font size',
                    "default" => 28,
                    "min" => 4,
                    "max" => 32,
                    "tooltip" => "to adjust price text font size",
                ],
                'title_font_size' => [
                    "type" => self::NUMBER,
                    "title" => 'Title font size',
                    "default" => 16,
                    "min" => 4,
                    "max" => 32,
                    "tooltip" => "to adjust Title font size",
                ],
                'text_font_size' => [
                    "type" => self::NUMBER,
                    "title" => 'Text font size',
                    "default" => 16,
                    "min" => 4,
                    "max" => 32,
                    "tooltip" => "to adjust text font size except price",
                ],
                'enable_fields' => [
                    "type" => self::SELECT2,
                    "title" => 'Fields that will display on Barcode',
                    "default" => 'name',
                    "options" => self::$barcodeFields,
                    'class' => 'form-control select2',
                    "tooltip" => "Add multiple fields that will be shown on barcode",
                ],
            ],

            'fbr' => [
                'is_fbr_enable' => [
                    "type" => self::CHECKBOX,
                    "title" => 'Enable FBR Invoice',
                    "default" => 0,
                    "tooltip" => "To Enable/Disable FBR Invoice"
                ],
                "fbr_url" => [
                    "type" => self::TEXTBOX,
                    "title" => 'FBR URL',
                    "default" => '',
                ],
                "fbr_pos_id" => [
                    "type" => self::NUMBER,
                    "title" => 'POS ID',
                    "default" => '',
                ],
            ],
        ];
        foreach (array_keys($settings) as $slug) {
            $title_list[self::$ModuleList[$slug]] = $slug;
        }
        ksort($title_list);
        foreach ($title_list as $title => $slug) {
            $sorted_settings[$slug] = $settings[$slug];
        }
        return $sorted_settings;
    }

    public static function GetCurrentSettings()
    {
        $settings = [];
        $data = self::select('module', 'key', 'value')->get();
        foreach ($data as $setting) {
            $settings[$setting->module][$setting->key] = $setting->value;
        }
        return $settings;
    }
}
