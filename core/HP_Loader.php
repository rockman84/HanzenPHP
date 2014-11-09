<?php
class HP_Loader extends CI_Loader{
	/** Plugin Loader **/
	public function plugin($plugin){
		if(!is_array($plugin)){
			$plugin = array($plugin);
		}
		$HP = &get_instance();
		foreach($plugin as $name){
			$this->_load_plugin($name);
		}
		return $HP->plugin;
	}
	/** plugin init class **/
	protected function _load_plugin($plugin,$params = null){
		$plugin = strtolower($plugin);
		$file_name = $plugin . '_plugin';
		if(!isset($HP->plugin->$plugin)){
			foreach(array(APPPATH,HP_PATH) as $path){
				if(file_exists($path.'/plugins/'.$file_name.EXT)){
					include_once($path.'/plugins/'.$file_name.EXT);
					$class = ucfirst($plugin).'_Plugin';
					if(class_exists($class)){
						$HP = &get_instance();
						$HP->plugin->$plugin = new $class;
					}
					else{
						show_error("Unable to load the requested class: ".$plugin);
					}
				}
				else{
					if(HP_PATH == $path){
						show_error('Unable to locate the plugin you have specified: '.$plugin);
						return;
					}
				}
			}
		}
	}

	public function model($model, $name = '', $db_conn = FALSE)
	{
		if (is_array($model))
		{
			foreach ($model as $babe)
			{
				$this->model($babe);
			}
			return;
		}

		if ($model == '')
		{
			return;
		}

		$path = '';

		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (($last_slash = strrpos($model, '/')) !== FALSE)
		{
			// The path is in front of the last slash
			$path = substr($model, 0, $last_slash + 1);

			// And the model name behind it
			$model = substr($model, $last_slash + 1);
		}

		if ($name == '')
		{
			$name = $model;
		}

		if (in_array($name, $this->_ci_models, TRUE))
		{
			return;
		}

		$CI =& get_instance();
		if (isset($CI->model->$name))
		{
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}

		$model = strtolower($model);

		foreach ($this->_ci_model_paths as $mod_path)
		{
			if ( ! file_exists($mod_path.'models/'.$path.$model.'.php'))
			{
				continue;
			}

			if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
			{
				if ($db_conn === TRUE)
				{
					$db_conn = '';
				}

				$CI->load->database($db_conn, FALSE, TRUE);
			}

			if ( ! class_exists('CI_Model'))
			{
				load_class('Model', 'core');
			}

			require_once($mod_path.'models/'.$path.$model.'.php');
			// add surfix _Model
			$model = ucfirst($model).'_Model';
			// add model property
			$CI->model->$name = new $model();

			$this->_ci_models[] = $name;
			return;
		}

		// couldn't find the model
		show_error('Unable to locate the model you have specified: '.$model);
	}
}
/* Location: ./core/HP_Loader.php */