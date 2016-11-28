<?php
	class GPIO {
		/* PRIVATE vars */
		var $_gpio_pin = "null";
		var $_gpio_direction = "null";
		/* CONTRUCTOR */
		public function __construct($_gpio_pin, $_gpio_direction) {
			$this->_set_gpio_pin($_gpio_pin);
			$this->_set_gpio_direction($_gpio_direction);	
		}
		/* PRIVATE METHODS */
		private function _set_gpio_pin($_gpio_pin) {
			$this->_gpio_pin = $_gpio_pin;
		}
		private function _set_gpio_direction($_gpio_direction) {
			if (strtolower($_gpio_direction) == "in" || strtolower($_gpio_direction) == "input")
				$_gpio_direction = "in";
			else if (strtolower($_gpio_direction) == "out" || strtolower($_gpio_direction) == "output")
				$_gpio_direction = "out";
			$this->_gpio_direction = $_gpio_direction;
			exec("gpio export ". $this->get_gpio_pin() ." ". $this->get_gpio_direction());			
		}
		
		/* PUBLIC METHODS */
		public function get_gpio_pin() {
			return $this->_gpio_pin;
		}
		public function get_gpio_direction() {
			return $this->_gpio_direction;
		}
		public function write($_state) {
			if (strtolower($_state) == 1 || strtolower($_state) == "1" || strtolower($_state) == "high" || strtolower($_state) == "hi" || strtolower($_state) == "on" )
				$_state = 1;
			else if (strtolower($_state) == 0 || strtolower($_state) == "0" || strtolower($_state) == "low" || strtolower($_state) == "lo" || strtolower($_state) == "off" )
				$_state = 0;
			exec( "gpio -g write ". $this->get_gpio_pin() ." ". $_state );
		}
		public function read() {
			//CHANGE the value inside exec to match 
			//"          sudo python *filename*              " basically linux command to run python file
			//to run the pythom file
			//to read. call this function where u want to start reading
			//not sure about the dots
			return exec( "gpio -g read ". $this->get_gpio_pin(), $status);
			// return $status;
		}
		
		public function endread(){
			//use control c to stop reading
			return exec("^c");	
		}
		// right now just create a new web page xxx.php that calls read function when we enter it 
		//and another that stops reading (calll endread) when we enter it
		//we dont need button atm
		
	}
?>
