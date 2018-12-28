<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 04.07.2018
 * Time: 10:10
 */

class xmlworker{

    private $xm;
    private $shop_link;

    public function createXML(){

        $this->xm = new XMLWriter();
        $this->xm->openURI('housefit.xml');
        //$this->xm->openMemory();
        $this->xm->startDocument("1.0","UTF-8");
        $this->xm->startDtd("yml_catalog", null ,"shops.dtd");
        $this->xm->endDtd();
        $this->xm->startElement("yml_catalog");
            $this->xm->writeAttribute("date",$this->getNowDate());
            $this->xm->startElement("shop");
                $this->xm->startElement("name");
                    $this->xm->text("Спортивный интернет магазин в Украине, спорттовары онлайн | HouseFit");
                $this->xm->endElement();
                $this->xm->startElement("company");
                    $this->xm->text("HOUSEFIT");
                $this->xm->endElement();
                $this->xm->startElement("url");
                    $this->xm->text("http://housefit.ua/");
                $this->xm->endElement();
                $this->xm->startElement("currencies");
                    $this->xm->startElement("currency");
                        $this->xm->writeAttribute("id","UAH");
                        $this->xm->writeAttribute("rate","1");
                    $this->xm->endElement();
                $this->xm->endElement();
                $this->xm->startElement("categories");
                    foreach ($this->getShopLink()->getCategories() as $category){
                        if(isset($category['child']) && count($category['child'])>0){
                            $this->xm->startElement("category");
                                $this->xm->writeAttribute("id",$category['id']);
                                $this->xm->text($category['title']);
                            $this->xm->endElement();
                            foreach($category['child'] as $child){
                                $this->xm->startElement("category");
                                    $this->xm->writeAttribute("id",$child['id']);
                                    $this->xm->writeAttribute("parentId",$child['parent_id']);
                                    $this->xm->text($child['title']);
                                $this->xm->endElement();
                            }
                        }
                        else{
                            $this->xm->startElement("category");
                                $this->xm->writeAttribute("id",$category['id']);
                                $this->xm->text($category['title']);
                            $this->xm->endElement();
                        }
                    }
                $this->xm->endElement();
                $this->xm->startElement("offers");
                    foreach($this->getShopLink()->getProducts() as $product){
                        $this->xm->startElement("offer");
                            $this->xm->startElement("url");
                                $this->xm->text($product['url']);
                            $this->xm->endElement();
                            $this->xm->writeAttribute("id",$product['id']);
                            $this->xm->writeAttribute("available",$product['available']);
                            $this->xm->startElement("price");
                                $this->xm->text($product['price']);
                            $this->xm->endElement();
                            $this->xm->startElement("currencyId");
                                $this->xm->text("UAH");
                            $this->xm->endElement();
                            $this->xm->startElement("categoryId");
                                $this->xm->text($product['category']);
                            $this->xm->endElement();
                            if(count($product['pictures'])>0){
                                foreach($product['pictures'] as $picture) {
                                    $this->xm->startElement("picture");
                                        $this->xm->text("http://housefit.ua".$picture['path']);
                                    $this->xm->endElement();
                                }
                            }
                            $this->xm->startElement("vendor");
                                $this->xm->text($product['vendor']);
                            $this->xm->endElement();
                            $this->xm->startElement("name");
                                $this->xm->text($product['title']);
                            $this->xm->endElement();
                            $this->xm->startElement("description");
                                $this->xm->startCdata();
                                    $this->xm->text($product['body']);
                                $this->xm->endCdata();
                            $this->xm->endElement();
                            if(count($product['params'])>0){
                                foreach ($product['params'] as $param) {
                                    $this->xm->startElement("param");
                                        $this->xm->writeAttribute("name", $param['title']);
                                        $this->xm->text($param['value']);
                                    $this->xm->endElement();
                                }
                            }
                            $this->xm->startElement("stock_quantity");
                                $this->xm->text("100");
                            $this->xm->endElement();
                        $this->xm->endElement();
                    }
                $this->xm->endElement();
            $this->xm->endElement();
        $this->xm->endElement();
        $this->xm->endDocument();
        $this->xm->flush();
//        file_put_contents('housefit.xml', $this->xm->flush(true));
//
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=housefit.xml");
        readfile("housefit.xml");
    }
    public function setShopLink($link){
        if(!empty($link)){
            $this->shop_link = $link;
        }
        return $this;
    }
    public function getShopLink(){
        if(!empty($this->shop_link)){
            return $this->shop_link;
        }
        else    return $this;
    }
    private function getNowDate(){
        return date("Y-m-d H:i");
    }

}