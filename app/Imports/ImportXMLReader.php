<?php

namespace App\Imports;

use App\Library\AbstractAdvertisementXMLReader;
use XMLReader;

class ImportXMLReader extends AbstractAdvertisementXMLReader
{

    protected $product_id = 0;
    protected $index = -1;

    public function __construct($xml_path)
    {
        parent::__construct($xml_path);
    }

    protected function _parse($name)
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == $name) {
            $item_value = NULL;
            $this->reader->read();
            if ($this->reader->nodeType == XMLReader::TEXT) $item_value = $this->reader->value;

            $this->result[$this->index][$name] = $item_value;
        }
    }

    protected function parse_refproduct()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'Ref.Product') {
            $this->index++;
            $this->result['product'][$this->index]['ref'] = $this->reader->getAttribute('Ref');
            $this->result['product'][$this->index]['code'] = $this->reader->getAttribute('Code');
            $this->result['product'][$this->index]['name'] = $this->reader->getAttribute('Name');
            $this->result['product'][$this->index]['analog'] = $this->reader->getAttribute('Analog');
            $this->result['product'][$this->index]['multip'] = $this->reader->getAttribute('Multip');
            $this->result['product'][$this->index]['group'] = $this->reader->getAttribute('Group');
            $this->result['product'][$this->index]['del'] = $this->reader->getAttribute('Del') == 'true' ? 1 : 0;
            $this->result['product'][$this->index]['manufacturer'] = $this->reader->getAttribute('Producer');
        }
    }

    protected function parse_regrest()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'Reg.Rest') {
            $this->index++;
            $this->result['state'][$this->index]['pharm'] = $this->reader->getAttribute('Pharm');
            $this->result['state'][$this->index]['product'] = $this->reader->getAttribute('Product');
            $this->result['state'][$this->index]['quantity'] = $this->reader->getAttribute('Qty');
            $this->result['state'][$this->index]['price'] = $this->reader->getAttribute('Price');
        }
    }

    protected function parse_refpharm()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'Ref.Pharm') {
            $this->index++;
            $this->result['pharm'][$this->index]['ref'] = $this->reader->getAttribute('Ref');
            $this->result['pharm'][$this->index]['name'] = $this->reader->getAttribute('Name');
            $this->result['pharm'][$this->index]['del'] = $this->reader->getAttribute('Del') == 'true' ? 1 : 0;
        }
    }

    protected function parse_refgroup()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'Ref.Group') {
            $this->index++;
            $this->result['group'][$this->index]['ref'] = $this->reader->getAttribute('Ref');
            $this->result['group'][$this->index]['name'] = $this->reader->getAttribute('Name');
            $this->result['group'][$this->index]['code'] = $this->reader->getAttribute('Code');
            $this->result['group'][$this->index]['is_group'] = $this->reader->getAttribute('IsGroup');
        }
    }

    protected function parse_refproducer()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'Ref.Producer') {
            $this->index++;
            $this->result['manufacturer'][$this->index]['ref'] = $this->reader->getAttribute('Ref');
            $this->result['manufacturer'][$this->index]['name'] = $this->reader->getAttribute('Name');
            $this->result['manufacturer'][$this->index]['import'] = $this->reader->getAttribute('Import');
            $this->result['manufacturer'][$this->index]['del'] = $this->reader->getAttribute('Del') == 'true' ? 1 : 0;
        }
    }

    protected function parse_messageno()
    {
        if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName == 'MessageNo') {
            $this->result['messageNo'] = $this->reader->expand()->textContent;
        }
    }

}