<?php
    namespace NetWorks\libs;
    include_once dirname(__DIR__).'/autoloader.php';
    /**
     * NetWorks plugins library
     */
    class Plugins{
        public function __construct() {
            
        }
        /**
         * List the plugins in the list
         * @return array List of plugin names
         */
        public function list(): array{
            return array_diff(scandir(NW_PLUGINS_DIR),['.','..']);
        }
        /**
         * Triggers plugin from hook
         * @param string $hookName Hook name
         * @param mixed ...$args arguments to pass
         * @return mixed Plugins hook
         */
        public function hook(string $hookName, mixed ...$args): mixed{
            foreach ($this->list() as $plugin) {
                $pluginClass = "NetWorks\\plugins\\$plugin";
                if (class_exists(class: $pluginClass) && method_exists(object_or_class: $pluginClass, method: $hookName))
                    return call_user_func([new $pluginClass(), $hookName],  ...$args);
            }
            return '';
        }
        /**
         * Checks if database exists
         * @return bool TRUE if exists, else FALSE
         */
        public function dbExits(): bool{
            return file_exists(filename: NW_DATABASE.NW_DS.'NetworksCMS.db');
        }
        /**
         * Formats data from table array
         * @param array|bool $selection Data selection
         * @return array Returns formatted selection, empty if selection wasn't possible
         */
        public function selFormat(array|bool $selection):array{
            return is_array(value: $selection) ? $selection : [];
        }
    }
?>