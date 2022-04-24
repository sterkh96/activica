<?php

namespace Sterkh\Activica;

use Exception;
use Sterkh\Activica\Model;

class Shop
{
    public $parsed;

    public function parse($path) {
         if (file_exists($path)) {
            $this->parsed = simplexml_load_file($path);
        }
    }

    public function view()
    {
        $model = new Model();
        // check for filters;
        $title = $model->getOption('company');
        $vendors = $model->getAll('vendors');

        $products = $model->getProducts();

        $props = [];
        foreach($model->getAll('product_props') as $p){
            $props[$p['product_id']][] = $p['value'];
        }

        $cats = [];
        foreach($model->getAll('categories') as $c) {
            $cats[$c['id']] = $c;
        }

        include __DIR__ . '/../templates/index.php';
    }
    public function isParsed() {
        $model = new Model();
        return $model->getOption('parsed');
    }
    public function save()
    {
        $model = new Model();
        $options = [
            'name' => (string)$this->parsed->shop->name,
            'company' => (string)$this->parsed->shop->company,
            'url' => (string)$this->parsed->shop->url
        ];
        foreach ($options as $key => $opt) {
            $model->setOption($key, $opt);
        }
        // categories;
        $cats = [];
        foreach ($this->parsed->shop->categories->category as $cat) {
            $attrs = $cat->attributes();
            $cats[(int)$attrs->parentId][] = array(
                'id' => (string)$attrs->id,
                'title' => (string)$cat,
                'parent_id' => (int)$attrs->parentId === 0 ? NULL : (int)$attrs->parentId
            );
        }
        foreach ($cats as $cat) {
            $model->multiInsert('categories', array_keys($cat[0]), $cat);
        }

        //products
        $products = [];
        $props = [];
        $vendors = [];
        foreach ($this->parsed->shop->offers->offer as $p) {
            $attrs = $p->attributes();
            $products[] = array(
                'id' => (string)$attrs->id,
                'name' => (string)$p->name,
                'url' => (string)$p->url,
                'price' => (float)$p->price,
                'optprice' => (float)$p->optprice,
                'category_id' => (int)$p->categoryId,
                'picture' => (string)$p->picture,
                'article' => (string)$p->articul,
                'description' => (string)$p->description,
                'available' => (string)$attrs->available === 'true' ? 1 : 0,
                'status_new' => (string)$p->statusNew === 'true' ? 1 : 0,
                'status_action' => (string)$p->statusAction === 'true' ? 1 : 0,
                'status_top' => (string)$p->statusTop === 'true' ? 1 : 0,
                'vendor' => (string)$p->vendor
            );
            foreach ($p->extprops as $extprops) {
                foreach ($extprops as $extprop) {
                    $props[] = array(
                        'product_id' => (int)$attrs->id,
                        'name' => $extprop->getName(),
                        'value' => (string)$extprop
                    );
                }
            }
            array_push($vendors, ['name' => (string)$p->vendor]);
        }

        $model->multiInsert('vendors', array_keys($vendors[0]), $vendors);
        $model->multiInsert('product_props', array_keys($props[0]), $props);

        $vnd = $model->getAll('vendors');
        $vndIds = [];
        foreach ($vnd as $vendor) {
            $vndIds[$vendor['name']] = $vendor['id'];
        }
        foreach ($products as &$p) {
            $p['vendor_id'] = $vndIds[$p['vendor']];
            unset($p['vendor']);
            unset($p);
        }
        $model->multiInsert('products', array_keys($products[0]), $products);

        $model->setOption('parsed', 1);

    }


}