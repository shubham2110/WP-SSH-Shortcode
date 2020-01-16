<?php

	class AvihastaPluginDeactivate{
	
		public static function deactivate(){
			flush_rewrite_rules();

		
		}
	
	}
