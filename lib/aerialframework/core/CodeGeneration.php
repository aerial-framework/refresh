<?php
    import("aerialframework.core.RelationshipBuilder");

    class CodeGeneration
    {
        /**
         * @route           /codegen/generate-models
         * @routeMethods    GET
         *
         * @return bool|void
         */
        public function getModelsFromDatabase()
        {
            $modelsPath = Configuration::get("PHP_MODELS");

            $options = array(
                "baseClassName"        => "Aerial_Record",
                "baseClassesDirectory" => "base"
            );

            return Aerial_Core::generateModelsFromDb($modelsPath, array("doctrine"), $options);
        }

        /**
         * @route               /codegen/yaml
         * @routeMethods        GET
         *
         * @return bool|void
         */
        public function generateYAMLFromDb()
        {
            $options = array(
                "baseClassName"        => "Aerial_Record",
                "baseClassesDirectory" => "base"
            );

            Doctrine_Manager::getInstance()->connection()->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);
            $definitions = Aerial_Core::generateDefinitionsFromDb($options);
            Doctrine_Manager::getInstance()->connection()->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, false);

            $yamlPath = Configuration::get("CONFIG_PATH")."/schema.yml";

            $definitions = RelationshipBuilder::build($definitions);
            file_put_contents($yamlPath, Doctrine_Parser_Yml::dump($definitions, "yml"));

            return true;
        }

        public function generateModelsFromYAML()
        {
            $yamlPath = Configuration::get("CONFIG_PATH")."/schema.yml";

            $options = array(
                "baseClassName"        => "Aerial_Record",
                "baseClassesDirectory" => "base"
            );

            Aerial_Core::generateModelsFromYaml($yamlPath, Configuration::get("PHP_MODELS"), $options);
            return true;
        }


        public function getAS3Type($type, $unsigned)
        {
            $as3type = "";
            switch($type)
            {
                case 'integer':
                    $as3type = $unsigned ? "uint" : "int";
                    break;
                case 'decimal':
                case 'float':
                case 'double':
                    $as3type = "Number";
                    break;
                case 'set':
                case 'array':
                    $as3type = "Array";
                    break;
                case 'boolean':
                    $as3type = "Boolean";
                    break;
                case 'blob':
                    $as3type = "ByteArray";
                    break;
                case 'object':
                    $as3type = "Object";
                    break;
                case 'time':
                case 'timestamp':
                case 'date':
                case 'datetime':
                    $as3type = "Date";
                    break;
                case 'enum':
                case 'gzip':
                case 'string':
                case 'clob':
                    $as3type = "String";
                    break;
                default:
                    $as3type = $type;
                    break;
            }

            return $as3type;
        }
    }
