<?php

class Yazi_yonetimi extends MY_EditorKontroller {
	
	function onay_bekleyenler() {
		
		$this->editor_lib->sadece_editor_gorebilir();
		
		$this->load->model('yazi_mod');

		$data['yazilar'] = $this->yazi_mod->get_liste_7();
		
		$data['meta_baslik'] = 'Onay Bekleyen Yazılar Listesi';
		
		// flash datalar set edilir
		$data['tamam'] = $this->session->flashdata('tamam');
		
		$data['icerik'] = $this->smarty->view('editor/yazi_yonetimi/onay_bekleyenler.tpl', $data, TRUE);

		$this->smarty->view( 'editor/layout2.tpl', $data );
	} 
	
	function onay_bekleyen_detay($id = 0) {
	
		$this->editor_lib->sadece_editor_gorebilir();
		
		$this->load->model('yazi_mod');
		$this->load->model('yazi_etiketi_mod');
		
		$this->yazi_mod->id = (int) $id;
		
		// yazı sistemde mevcut olmalı
		if (!$this->yazi_mod->is_var_where_id())
			redirect(SAYFA_EDITOR_11);
			
		// yazı editör kontrolünden geçecek durumda olmalı
		if ($this->yazi_mod->get_durum_where_id() != Yazi_mod::DURUM_EDITOR_KONTROL_EDECEK)
			redirect(SAYFA_EDITOR_11);
			
		$this->yazi_etiketi_mod->yazi_id = $this->yazi_mod->id;
			
		$data['yazi'] = $this->yazi_mod->get_detay_2(); 
		$data['yazi_etiketleri'] = $this->yazi_etiketi_mod->get_liste_2();
		
		$data['meta_baslik'] = 'Onay Bekleyen Yazı Detay';
		
		$data['icerik'] = $this->smarty->view('editor/yazi_yonetimi/onay_bekleyen_detay.tpl', $data, TRUE);

		$this->smarty->view( 'editor/layout2.tpl', $data );
	}
	
	// editörün bekleyen yazıyı yayınlaması için kullanılır.
	function onay_bekleyen_yayinla($id = 0) {
	
		$this->editor_lib->sadece_editor_gorebilir();
		
		$this->load->model('yazi_mod');
		
		$this->yazi_mod->id = (int) $id;
		
		// yazı sistemde mevcut olmalı
		if (!$this->yazi_mod->is_var_where_id())
			redirect(SAYFA_EDITOR_11);
			
		// yazının durumu editör kontrol edecek olmalı
		if ($this->yazi_mod->get_durum_where_id() != Yazi_mod::DURUM_EDITOR_KONTROL_EDECEK)
			redirect(SAYFA_EDITOR_11);
			
		// yazının durumunu onaylı yap
		$this->yazi_mod->durum = Yazi_mod::DURUM_ONAYLI;
		$this->yazi_mod->guncelle_durum_where_id();
		
		// yazı onaylandığı için ping gönderilecek
		$this->load->library('ping_lib');
		$this->ping_lib->yaziyi_weblogsa_gonder($this->yazi_mod->id);
		
		
		$this->load->model('yazar_mod');
		$this->yazar_mod->id = $this->yazi_mod->get_yazar_id_where_id();
		$data['yazar'] = $this->yazar_mod->get_detay_where_id();
		
		// yazı detay sayfasının adresi
		$data['url1'] = sprintf(SAYFA_MISAFIR_23, $this->yazi_mod->id);
		
		// yazısı onaylanan yazara mail ile bilgi ver.
		// basla mail
		$this->load->library('email');
		
		$this->email->to($data['yazar']->mail, $data['yazar']->adi);
		$this->email->subject('Yazınız Onaylandı');
		$this->email->message($this->smarty->view('editor/yazi_yonetimi/mailler/yazi_onaylandi.tpl', $data, true));
		
		$this->email->send();
		
		// echo $this->email->print_debugger();
		// bitti mail
		
		$this->session->set_flashdata('tamam', 'Yazı onaylandı ve yazar bilgilendirildi.');
		
		redirect(SAYFA_EDITOR_11);
	}
	
	// Editör isterse bir yazıyı adminlerin incelemesini isteyebilir
	function onay_bekleyeni_admin_kontrol_etsin($id = 0) {
		
		$this->editor_lib->sadece_editor_gorebilir();
		
		$this->load->model('yazi_mod');
		
		$this->yazi_mod->id = (int) $id;
		
		// yazı sistemde mevcut olmalı
		if (!$this->yazi_mod->is_var_where_id())
			redirect(SAYFA_EDITOR_11);
			
		// yazının durumu editör kontrol edecek olmalı
		if ($this->yazi_mod->get_durum_where_id() != Yazi_mod::DURUM_EDITOR_KONTROL_EDECEK)
			redirect(SAYFA_EDITOR_11);
			
		// yazının durumunu admin kontrol edecek olarak değiştir
		$this->yazi_mod->durum = Yazi_mod::DURUM_ADMIN_KONTROL_EDECEK;
		$this->yazi_mod->guncelle_durum_where_id();

		// adminin giriş yapabileceği adres
		$data['url1'] = SAYFA_ADMIN_1;
		
		// adminlere mail gönder
		$this->load->library('email');
		$this->load->model('admin_mod');
		$adminler = $this->admin_mod->get_liste_1();
		foreach ($adminler->result() as $admin) {
		
			$data['admin'] = $admin;
			
			// basla mail
			$this->email->to($admin->mail, $admin->adi);
			$this->email->subject('Editör Yazıyı Kontrol Etmenizi İstedi');
			$this->email->message($this->smarty->view('editor/yazi_yonetimi/mailler/admin_kontrol_etsin.tpl', $data, true));
			
			$this->email->send();
			
			// echo $this->email->print_debugger();
			// bitti mail
		}
		
		$this->session->set_flashdata('tamam', 'Yazıyı adminler inceleyecek, gerekli bilgi gönderildi.');
		
		redirect(SAYFA_EDITOR_11);
	}
	

	// editör isterse yazıyı yazan kişinin yazı üzerinde değişiklik yapmasını isteyebilir 
	function onay_bekleyeni_yazar_kontrol_etsin($id = 0) {
	
		$this->editor_lib->sadece_editor_gorebilir();
		
		$this->load->model('yazi_mod');
		
		$this->yazi_mod->id = (int) $id;
		
		// yazı sistemde mevcut olmalı
		if (!$this->yazi_mod->is_var_where_id())
			redirect(SAYFA_EDITOR_11);
			
		// yazının durumu editör kontrol edecek olmalı
		if ($this->yazi_mod->get_durum_where_id() != Yazi_mod::DURUM_EDITOR_KONTROL_EDECEK)
			redirect(SAYFA_EDITOR_11);
			
		// yazıyı yazar tekrar inceleyecek
		$this->yazi_mod->durum = Yazi_mod::DURUM_YAZAR_KONTROL_EDECEK;
		$this->yazi_mod->guncelle_durum_where_id();
		
		$this->load->model('yazar_mod');
		$this->yazar_mod->id = $this->yazi_mod->get_yazar_id_where_id();
		$data['yazar'] = $this->yazar_mod->get_detay_where_id();
		
		// yazar için giriş yapabileceği adres
		$data['url1'] = SAYFA_YAZAR_1;
		
		// yazıyı incelemesi gerektiğini yazara mail ile bildir
		// basla mail
		$this->load->library('email');
		
		$this->email->to($data['yazar']->mail, $data['yazar']->adi);
		$this->email->subject('Yazınızı Yeniden Gözden Geçiriniz');
		$this->email->message($this->smarty->view('editor/yazi_yonetimi/mailler/yazar_kontrol_etsin.tpl', $data, true));
		
		$this->email->send();
		
		// echo $this->email->print_debugger();
		// bitti mail
		
		$this->session->set_flashdata('tamam', 'Yazara durum hakkında bilgi gönderildi.');
		
		redirect(SAYFA_EDITOR_11);
	}
}