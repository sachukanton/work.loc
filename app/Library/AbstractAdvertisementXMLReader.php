<?php

    namespace App\Library;

    use XMLReader;

    class AbstractAdvertisementXMLReader
    {

        protected $reader;
        protected $result = array();

        protected $_eventStack = array();

        public function __construct($xml, $file = TRUE)
        {
            if ($xml) {
                $this->reader = new XMLReader();
//                if ($file && is_file($xml)) {
//                    $this->reader->open($xml);
//                } elseif ($xml) {
                    $this->reader->xml($xml);
//                }

            }
        }

        public function parse()
        {
            if (isset($this->reader) && $this->reader) {
                $this->reader->read();
                while ($this->reader->read()) {
                    if ($this->reader->nodeType == XMLReader::ELEMENT) {
                        $fnName = str_slug('parse_' . $this->reader->localName, '_');
                        if (method_exists($this, $fnName)) {
                            $lcn = $this->reader->name;
                            $this->fireEvent('beforeParseContainer', array('name' => $lcn));
                            if ($this->reader->name == $lcn && $this->reader->nodeType != XMLReader::END_ELEMENT) {
                                $this->fireEvent('beforeParseElement', array('name' => $lcn));
                                $this->{$fnName}();
                                $this->fireEvent($fnName);
                                $this->fireEvent('afterParseElement', array('name' => $lcn));
                            } elseif ($this->reader->nodeType == XMLReader::END_ELEMENT) {
                                $this->fireEvent('afterParseContainer', array('name' => $lcn));
                            }
                        }
                    }
                }
            }
        }

        public function onEvent($event, $callback)
        {
            if (!isset($this->_eventStack[$event])) $this->_eventStack[$event] = array();
            $this->_eventStack[$event][] = $callback;

            return $this;
        }

        public function fireEvent($event, $params = NULL, $once = FALSE)
        {
            if ($params == NULL) $params = array();
            $params['context'] = $this;
            if (!isset($this->_eventStack[$event])) return FALSE;
            $count = count($this->_eventStack[$event]);
            if (isset($this->_eventStack[$event]) && $count > 0) {
                for ($i = 0; $i < $count; $i++) {
                    call_user_func_array($this->_eventStack[$event][$i], $params);
                    if ($once == TRUE) array_splice($this->_eventStack[$event], $i, 1);
                }
            }
        }

        public function getResult()
        {
            return $this->result;
        }

        public function clearResult()
        {
            $this->result = array();
        }

    }