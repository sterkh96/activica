<?php

namespace Sterkh\Activica;

use Sterkh\Activica\Model;

class Shop
{
    private $model;

    public function __construct()
    {
        $this->model = new Model();
        if (!$this->model->getOption('parsed')) {
            $data = $this->parse(__DIR__ . '/goods.xml');
            $this->save($data);
        }
        $this->view();
    }

    private function parse($path)
    {
        if (file_exists($path)) {
            return simplexml_load_file($path);
        }
        return false;
    }

    private function view()
    {
        // check for filters;
        $title = $this->model->getOption('company');
        $vendors = $this->model->getAll('vendors');

        $products = $this->model->getProducts();

        $props = [];
        foreach ($this->model->getAll('product_props') as $p) {
            $props[$p['product_id']][] = $p['value'];
        }

        $cats = [];
        foreach ($this->model->getAll('categories') as $c) {
            $cats[$c['id']] = $c;
        }

        include __DIR__ . '/../templates/index.php';
    }

    private function save($data)
    {
        $options = [
            'name' => (string)$data->shop->name,
            'company' => (string)$data->shop->company,
            'url' => (string)$data->shop->url
        ];
        foreach ($options as $key => $opt) {
            $this->model->setOption($key, $opt);
        }
        // categories;
        $cats = [];
        foreach ($data->shop->categories->category as $cat) {
            $attrs = $cat->attributes();
            $cats[(int)$attrs->parentId][] = array(
                'id' => (string)$attrs->id,
                'title' => (string)$cat,
                'parent_id' => (int)$attrs->parentId === 0 ? NULL : (int)$attrs->parentId
            );
        }
        foreach ($cats as $cat) {
            $this->model->multiInsert('categories', array_keys($cat[0]), $cat);
        }

        //products
        $products = [];
        $props = [];
        $vendors = [];
        foreach ($data->shop->offers->offer as $p) {
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

        $this->model->multiInsert('vendors', array_keys($vendors[0]), $vendors);
        $this->model->multiInsert('product_props', array_keys($props[0]), $props);

        $vnd = $this->model->getAll('vendors');
        $vndIds = [];
        foreach ($vnd as $vendor) {
            $vndIds[$vendor['name']] = $vendor['id'];
        }
        foreach ($products as &$p) {
            $p['vendor_id'] = $vndIds[$p['vendor']];
            unset($p['vendor']);
            unset($p);
        }
        $this->model->multiInsert('products', array_keys($products[0]), $products);

        $this->model->setOption('parsed', 1);

    }


}