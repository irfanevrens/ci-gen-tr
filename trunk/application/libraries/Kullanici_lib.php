<?php

class Kullanici_lib { 

	var $CI;
	
	var $kullanici_id ;
	var $kullanici_adi;
	var $kullanici_mail;
	var $kullanici_turu;  
	
	function Kullanici_lib() {
	
		$this->CI =& get_instance();
		
		$this->kullanici_turu = Kullanici::TUR_MISAFIR;
	}
	
	function init($kullanici_turu = 0) {
		
		$this->CI->kullanici->turu = $kullanici_turu;
		
		if ($detay = $this->CI->kullanici->get_detay_for_kullanici_lib()) {
		
			$this->kullanici_id = (int) $detay->id;
			$this->kullanici_adi = $detay->adi;
			$this->kullanici_mail = $detay->mail;
			$this->kullanici_turu = (int) $detay->turu;
		}
	}
	
	function sadece_admin_gorebilir() { if (!$this->is_admin()) redirect(SAYFA_ADMIN_1); }
	function sadece_yazar_gorebilir() { if (!$this->is_yazar()) redirect(SAYFA_YAZAR_1); }
	function sadece_editor_gorebilir() { if (!$this->is_editor()) redirect(SAYFA_EDITOR_1); }

	function is_admin() { return $this->kullanici_turu == Kullanici::TUR_ADMIN; }
	function is_yazar() { return $this->kullanici_turu == Kullanici::TUR_YAZAR; }
	function is_editor() { return $this->kullanici_turu == Kullanici::TUR_EDITOR; }
}