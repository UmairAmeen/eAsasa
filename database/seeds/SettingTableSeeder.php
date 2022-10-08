<?php

use Illuminate\Database\Seeder;
use App\Setting;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class SettingTableSeeder extends Seeder {

    public function run()
    {
        // TestDummy::times(20)->create('App\Post');
        if (!Setting::where('key','company')->first()) {
            $setting = new Setting();
            $setting->key = "company";
            $setting->name="Company Name";
            $setting->type="text";
            $setting->save();
        }

        if (!Setting::where('key','address')->first()) {
            $setting = new Setting();
            $setting->key = "address";
            $setting->name="Company Address";
            $setting->type="text";
            $setting->save();
        }

        if (!Setting::where('key','invoice_header')->first()) {
            $setting = new Setting();
            $setting->key = "invoice_header";
            $setting->name="Invoice Heading";
            $setting->type="textarea";
            $setting->save();
        }

        if (!Setting::where('key','invoice_footer')->first()) {
            $setting = new Setting();
            $setting->key = "invoice_footer";
            $setting->name="Invoice Footer";
            $setting->type="textarea";
            $setting->save();
        }

        if (!Setting::where('key','phone')->first()) {
            $setting = new Setting();
            $setting->key = "phone";
            $setting->name="Company Phone";
            $setting->type="tel";
            $setting->save();
        }

        if (!Setting::where('key','notification_phone')->first()) {
            $setting = new Setting();
            $setting->key = "notification_phone";
            $setting->name="Notification Phone";
            $setting->type="tel";
            $setting->save();
        }

        if (!Setting::where('key','notification_line')->first()) {
            $setting = new Setting();
            $setting->key = "notification_line";
            $setting->name="Notification Line ID";
            $setting->type="text";
            $setting->save();
        }

        if (!Setting::where('key','barcode')->first()) {
            $setting = new Setting();
            $setting->key = "barcode";
            $setting->name="Show Barcode on Invoice";
            $setting->type="checkbox";
            $setting->save();
        }

        if (!Setting::where('key','use_customer_pricing')->first()) {
            $setting = new Setting();
            $setting->key = "use_customer_pricing";
            $setting->name="Use Customer Pricing for each product sale";
            $setting->type="checkbox";
            $setting->value = 1;
            $setting->save();
        }

        if (!Setting::where('key','invoice_terms')->first()) {
            $setting = new Setting();
            $setting->key = "invoice_terms";
            $setting->name="Terms & Conditions for Sale Invoice";
            $setting->type="textarea";
            $setting->value = "";
            $setting->save();
        }

        if (!Setting::where('key','tax_percentage')->first()) {
            $setting = new Setting();
            $setting->key = "tax_percentage";
            $setting->name="Sale Tax Percentage";
            $setting->type="number";
            $setting->value = "16";
            $setting->save();
        }



        /*if (!Setting::where('key','logo')->first()) {
            $setting = new Setting();
            $setting->key = "logo";
            $setting->name="Company Logo";
            $setting->type="file";
            $setting->save();
        }*/

        Setting::where('key','version')->delete();
        $setting = new Setting();
        $setting->key = "version";
        $setting->name="Software Version";
        $setting->type="static";
        $setting->value="1.00";
        $setting->save();
    }

}