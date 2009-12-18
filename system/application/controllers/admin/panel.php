<?php

class Panel extends MY_AdminKontroller {

	function Panel() {
	
		parent::MY_AdminKontroller();
		
		$this->load->helper('form');
	}
	
	function index() {
	
		$this->kullanici_lib->sadece_admin_gorebilir();
		
		$data['admin_adi'] = $this->kullanici_lib->kullanici_adi;
		
		$this->template->write('menu_linkleri', $this->menu_lib->get_admin_linkleri());
		
		$this->template->write_view('icerik', 'admin/panel/index', $data);
		
		$this->template->render();
	} 
	
	function giris() {
	
		$this->kullanici_lib->sadece_misafir_gorebilir();		
		$this->template->write('menu_linkleri', $this->menu_lib->get_admin_linkleri('giris_yap'));
		
		$data['kullanici'] = $this->kullanici;
		
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
		
			$this->kullanici->mail = $this->input->post('mail');
			$this->kullanici->sifre = $this->input->post('sifre');
			
			try {
			
				if (form_is_bos($this->kullanici->mail)) throw new Exception('Mail adresiniz yazınız.');
				if (!form_is_mail($this->kullanici->mail)) throw new Exception('Lütfen geçerli bir mail adresi giriniz.');
				if (form_is_bos($this->kullanici->sifre)) throw new Exception('Şifrenizi yazınız.');				
				if (!$this->kullanici->is_var_admin_where_mail_and_sifre()) throw new Exception('Lütfen bilgileriniz kontrol ederek yeniden deneyiniz.');

				$this->kullanici_oturumu->kullanici_id = (int) $this->kullanici->get_admin_id_where_mail();
				$this->kullanici_oturumu->ekle();
				
				redirect(sayfa_admin_2);
			} catch (Exception $ex) {
			
				$data['hata'] = $ex->getMessage();
			}
		}
		
		$this->template->write_view('icerik', sayfa_admin_1, $data);
		
		$this->template->render();
	}
	
	function cikis() {
	
		$this->kullanici_lib->sadece_admin_gorebilir();
		
		$this->kullanici_oturumu->sil_where_oturum_id();
		
		redirect(sayfa_admin_1);
	} 
	
	function sifremi_unuttum() {
	
		$this->kullanici_lib->sadece_misafir_gorebilir();
		
		$this->template->write('menu_linkleri', $this->menu_lib->get_admin_linkleri('sifremi_unuttum'));
	
		$data['kullanici'] = $this->kullanici;
	
		if ($this->input->server('REQUEST_METHOD') == 'POST') {	
		
			$this->kullanici->mail = $this->input->post('mail');
			
			try {
			
				if (form_is_bos($this->kullanici->mail)) throw new Exception('Mail adresiniz yazınız.');
				if (!form_is_mail($this->kullanici->mail)) throw new Exception('Lütfen geçerli bir mail adresi giriniz.');
				if (!$this->kullanici->is_var_admin_where_mail()) throw new Exception('Mail adresi bulunamadı.');
				
				// yeni temp bilgisini al ve güncelle
				$temp_kullanici = $this->kullanici->get_admin_detay_where_mail();
				$this->kullanici->id = $temp_kullanici->id;
				$this->kullanici->adi = $temp_kullanici->adi;
				$this->kullanici->temp = $this->kullanici->get_rasgele_md5();
				$this->kullanici->guncelle_temp_where_id();
				
				// üyeye şifresini sıfırlaması için mail gönder
				$data['url1'] = site_url(sprintf(sayfa_admin_5, $this->kullanici->id, $this->kullanici->temp));
				
				// basla mail
				$this->load->library('email');

				$this->email->to($this->kullanici->mail, $this->kullanici->adi);
				$this->email->subject('Şifrenizi Sıfırlayınız');
				$this->email->message($this->load->view('admin/panel/mailler/sifremi_unuttum', $data, true));
				
				$this->email->send();
				
				// echo $this->email->print_debugger();
				// bitti mail
				
				$data['tamam'] = 'Yeni şifrenizi belirlemek için mail adresinizi kontrol ediniz.';
				
			} catch (Exception $ex) {
			
				$data['hata'] = $ex->getMessage();
			}
		} else {
			
		}
		
		$this->template->write_view('icerik', sayfa_admin_3, $data);
		
		$this->template->render();
	}
	
	function sifreyi_sifirla($id = 0, $temp = '') {
	
		$this->kullanici_lib->sadece_misafir_gorebilir();
		
		$this->template->write('menu_linkleri', $this->menu_lib->get_admin_linkleri('sifremi_unuttum'));
		
		$data['kullanici'] = $this->kullanici;
		
		$this->kullanici->id = (int) $id;
		$this->kullanici->temp = $temp;
		
		try {
		
			if (!$this->kullanici->is_var_where_id_and_temp()) throw new Exception('Lütfen yeniden şifremi unuttum formunu doldurunuz.');
			
			$temp_kullanici = $this->kullanici->get_detay_where_id();
			
			$this->kullanici->adi = $temp_kullanici->adi;
			$this->kullanici->mail = $temp_kullanici->adi;
			
			$temp_sifre = $this->kullanici->get_rasgele_sifre();
			$this->kullanici->sifre = $temp_sifre;
			$this->kullanici->guncelle_sifre_where_id();
			$data['temp_sifre'] = $temp_sifre;
			
			$this->kullanici->temp = $this->kullanici->get_rasgele_md5();
			//$this->kullanici->guncelle_temp_where_id();
			
			$data['url1'] = site_url(sayfa_admin_1);
			
			// basla mail
			$this->load->library('email');

			$this->email->to($this->kullanici->mail, $this->kullanici->adi);
			$this->email->subject('Yeni Şifreniz');
			$this->email->message($this->load->view('admin/panel/mailler/sifreyi_sifirla', $data, true));
			
			$this->email->send();
			
			// echo $this->email->print_debugger();
			// bitti mail
			
			$data['tamam'] = 'Şifreniz sıfırlanıp yeni şifreniz mail adresinize gönderilmiştir.';
		} catch (Exception $ex) {
		
			$data['hata'] = $ex->getMessage();
		}
		
		$this->template->write_view('icerik', 'admin/panel/sifreyi_sifirla', $data);
		
		$this->template->render();
	}
}