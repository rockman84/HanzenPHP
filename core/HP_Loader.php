<?php
class HP_Loader extends CI_Loader{
	/** Plugin Loader **/
	public function plugin($plugin){
		return $this->_load_plugin($plugin);
	}
	/**
	* plugin init class
	*
	* @param array | string
	* @return void
	**/
	protected function _load_plugin($plugin){
		$HP = &get_instance();
		if(is_array($plugin)){
			foreach($plugin as $val){
				$this->_load_plugin($val);
			}
			return $HP->plugin;
		}
		$pl = &load_file('plugins/'.$plugin,'','_Plugin');
		// check class is exist
		if($pl){
			$name = strtolower($pl['name']);
			$HP->plugin->$name = $pl['return'];
			return $HP->plugin->$name;
		}
		else{
			show_error("Unable to load the requested class: ".$plugin);
		}
	}
	/**
	 * Model Loader (modified)
	 *
	 * This function lets users load and instantiate models.
	 *
	 * @param	string	the name of the class
	 * @param	string	name for the model
	 * @param	bool	database connection
	 * @return	void
	 */
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

/* End of file HP_Loader.php */
/* Location: ./core/HP_Loader.php */