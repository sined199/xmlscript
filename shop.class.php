<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 03.07.2018
 * Time: 14:59
 */

class shop{

    private $db_link = null;
    private $buffer = array();
    private $categories = array();
    private $products = array();

    public function getCategories(){
        return $this->categories;
    }

    public function setCategories(){
        $categoriesDB = $this->getDB()->select("*","categories");

        $this->categories = $this->fetchdataCategories($categoriesDB);
        return $this;
    }

    public function setDB($link){
        if(!empty($link)) {
            $this->db_link = $link;
        }

        return $this;
    }
    private function getDB(){
        if($this->db_link!=null) {
            return $this->db_link;
        }
        else{
            return false;
        }
    }
    private function fetchdataCategories($data){
        $fetchedData = array();

        foreach ($data as $category) {
            $fetchedData[] = array(
                'id' => $category['id'],
                'title' => $category['title'],
                'parent_id' => $category['parent_id'],
                'slug' => $category['slug']
            );
        }

        usort($fetchedData,function ($item1, $item2){
            if($item1['id'] == $item2['id']) return 0;
            return ($item1['id'] < $item2['id']) ? -1 : 1;
        });

        $threeCategories = array();

        foreach ($fetchedData as $data) {
            foreach($this->buffer as $key => $buff){
                for($i=0;$i<count($threeCategories);$i++){
                    if($buff['parent_id'] == $threeCategories[$i]['id']){
                        $threeCategories[$i]['child'][] = $buff;
                        unset($this->buffer[$key]);
                    }
                }
            }
            if ($data['parent_id'] == 0) {
                $threeCategories[] = $data;
            } else {
                $empty = true;
                for ($i = 0; $i < count($threeCategories); $i++) {
                    if ($threeCategories[$i]['id'] == $data['parent_id']){
                        $threeCategories[$i]['child'][] = $data;
                        $empty = false;
                    }
                }
                if($empty){
                    $this->buffer[] = $data;
                }
            }
        }
        unset($this->buffer);
        return $threeCategories;
    }

    public function getProducts(){
        return $this->products;
    }
    public function setProducts(){
        $products = $this->getDB()->select("p.id,p.category_id,p.price,p.slug,p.title,p.body,p.available, b.title as brand","products p INNER JOIN brands b ON p.brand_id = b.id");

        foreach ($products as $product) {
            $prod = array(
                'id' => $product['id'],
                'category' => $product['category_id'],
                'price' => (int)$product['price'],
                'slug' => $product['slug'],
                'title' => $product['title'],
                'body' => $product['body'],
                'available' => ($product['available'] == 1) ? "true" : "false",
                'vendor' => $product['brand'],
                'pictures' => $this->setImagesInfoProducts($product['id']),
                'params' => $this->setParamsInfoProducts($product['id']),
                'url' => "http://housefit.ua".$this->setUrlInfoProducts($product['slug'],$product['category_id'])
            );
            $this->products[] = $prod;
        }
        return $this;
    }
    private function setImagesInfoProducts($id_product){
        $images = $this->getDB()->select("i.path","images i INNER JOIN product_product_image ppi ON i.id = ppi.product_image_id","ppi.product_id = $id_product");
        return $images;
    }
    private function setParamsInfoProducts($id_product){
        $params = $this->getDB()->select("f.title,fv.value","filter_product fp INNER JOIN filters f ON fp.filter_id = f.id INNER JOIN filter_values fv ON fp.filter_value_id = fv.id","fp.product_id = $id_product");
        return $params;
    }
    private function setUrlInfoProducts($slug,$category_id){
        $url = "";
        $urlslug = array();

        $categories = $this->getCategories();

        $urlslug = $this->loopSearch($categories,$category_id);

        for($i=count($urlslug)-1;$i>=0;$i--){
            $url .= "/".$urlslug[$i];
        }

        $url .= "/".$slug;

        return $url;

    }
    private function loopSearch($categories,$category_id){
        $urlslug = array();
        foreach ($categories as $category){
            if(isset($category['child']) && count($category['child'])>0){
                $slug = $this->loopSearch($category['child'],$category_id);
                if(count($slug)>0) {
                    foreach ($slug as $s) {
                        $urlslug[] = $s;
                    }
                }
            }
            else{
                if($category['id'] == $category_id) {
                    $urlslug[] = $category['slug'];

                }
            }
        }

        return $urlslug;
    }
}