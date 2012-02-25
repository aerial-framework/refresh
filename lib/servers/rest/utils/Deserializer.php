<?php
    require_once(dirname(__FILE__) . "/middleware/DeserializerMiddleware.php");

    class Deserializer extends DeserializerMiddleware
    {
        public function __construct($app, $settings = array())
        {
            parent::__construct($app, $settings);

            $this->contentTypes = array_merge(array(
                                                   'application/json' => array($this, 'parseJson'),
                                                   'application/xml'  => array($this, 'parseXml'),
                                                   'text/xml'         => array($this, 'parseXml'),
                                                   'text/csv'         => array($this, 'parseCsv')
                                              ), $settings);
        }


        /**
         * Parse input
         *
         * This method will attempt to parse the request body
         * based on its content type if available.
         *
         * @param   string $input
         * @param   string $contentType
         * @return  mixed
         */
        protected function parse($input, $contentType)
        {
            if(isset($this->contentTypes[$contentType]) && is_callable($this->contentTypes[$contentType]))
            {
                $result = call_user_func($this->contentTypes[$contentType], $input);
                if($result)
                {
                    return $result;
                }
            }
            return $input;
        }

        /**
         * Parse JSON
         *
         * This method converts the raw JSON input
         * into an associative array.
         *
         * @param   string $input
         * @return  array|string
         */
        protected function parseJson($input)
        {
            if(function_exists('json_decode'))
            {
                return json_decode($input);
            } else
            {
                return $input;
            }
        }

        /**
         * Parse XML
         *
         * This method creates a SimpleXMLElement
         * based upon the XML input. If the SimpleXML
         * extension is not available, the raw input
         * will be returned unchanged.
         *
         * @param   string $input
         * @return  SimpleXMLElement|string
         */
        protected function parseXml($input)
        {
            if(class_exists('SimpleXMLElement'))
            {
                try
                {
                    // read XML, merge CDATA elements into text nodes
                    $xml = new SimpleXMLElement($input, LIBXML_NOCDATA);

                    return json_encode((object) ((array) $xml));
                } catch(Exception $e)
                {
                    throw new Exception("Unable to parse input data as XML.<br/>".$e->getMessage());
                }
            }
            return $input;
        }

        /**
         * Parse CSV
         *
         * This method parses CSV content into a numeric array
         * containing an array of data for each CSV line.
         *
         * @param   string $input
         * @return  array
         */
        protected function parseCsv($input)
        {
            $temp = fopen('php://memory', 'rw');
            fwrite($temp, $input);
            fseek($temp, 0);
            $res = array();
            while(($data = fgetcsv($temp)) !== false)
            {
                $res[] = $data;
            }
            fclose($temp);
            return $res;
        }

    }