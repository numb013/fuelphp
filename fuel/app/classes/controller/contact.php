<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
 class Controller_Contact extends Controller
 {
	 //入力フォームで取り扱うフィールドを配列として設定
	 //name：名前、email：メールアドレス、msg：本文
	 private $fields = array('name','email','msg');

	 public function action_index()
	 	{
	 		//フォームのsubmitボタンを押されたとき
	 		if (Input::post('submit'))
	 		{
	 			//postされた各データをフラッシュセッションに保存
	 			foreach ($this->fields as $field)
	 			{
	 				Session::set_flash($field, Input::post($field));
	 			}
	 		}
	 		//入力チェックのためのvalidationオブジェクトを呼び出す
	 		$val = Validation::forge();

	 		//各項目に対して、入力の検証ルールを設定する
	 		//名前を入力必須にする
	 		$val->add('name','名前')->add_rule('required');
	 		//メールアドレスを入力必須、正しいアドレス形式かチェック
	 		$val->add('email','メールアドレス')->add_rule('required')->add_rule('valid_email');
	 		//内容を入力必須にする
	 		$val->add('msg','内容')->add_rule('required');
	 		//$vai->run()で、入力チェックの実行
	 		//Security::check_token()で、hiddenでpostされたCSRFトークンをチェック
	 		if($val->run() and Security::check_token())
	 		{
	 			//それぞれのチェックが成功したら、確認画面にリダイレクト
	 			Response::redirect('contact/conf');
	 		}
	 		//ビューに渡すデータの配列を作る
	 		$data = array();
	 		//validationオブジェクトを配列に保存
	 		$data['val'] = $val;
	 		//$dataをビューに埋め込み、ビューを表示する
	 		return View::forge('contact/index',$data);
	 	}

		public function action_conf()
		{
			//データ用の配列初期化
			$data = array();
			//入力のときに保存したセッションデータを配列に保存
			foreach ($this->fields as $field)
			{
				$data[$field] = Session::get_flash($field);
				//セッション変数を次のリクエストを維持
				Session::keep_flash($field);
			}
			//データをビューに渡す
			$view = View::forge('contact/conf',$data);
			return $view;
		}

		public function action_send()
		{
			//確認画面で「修正ボタン」押下
			if (Input::post('back'))
			{
				//各フィールドのセッション期限延期
				foreach ($this->fields as $field)
				{
					Session::keep_flash($field);
				}
				//入力画面にリダイレクト
				Response::redirect('/contact/index');
			}
			//csrfトークンをチェック->NGのとき
			if(!Security::check_token() )
			{
				$data['message'] = "ページ遷移が正しくありません";
				$view = View::forge('contact/send',$data);
				return $view;
			}
			//セッション確認、リロード対策
			if(Session::get_flash('email'))
			{
				//メール本文のビューデータ初期化
				$mail = array();
				//各フィールドのデータをセッションから取得
				foreach ($this->fields as $field)
				{
					//メール本文用のフィールドにデータを代入
					$mail[$field] = Session::get_flash($field);
				}
				//メールのビュー呼び出し
				$body = View::forge('contact/contact_mail',$mail);
				//Emailオブジェクト
				$email = Email::forge();
				//from設定
				$email->from(Session::get_flash('email'), Session::get_flash('name'));
				//to設定
				$email->to(Config::get('contact/contact_to'));
				//件名
				$email->subject('【fuweb.info】お問い合わせ');
				//bodyをエンコードして設定
				$email->body(mb_convert_encoding($body, 'jis'));
				//送信
				try
				{
					$email->send();
				}
				catch(\EmailValidationFailedException $e)
				{
					//メールアドレスの検証失敗
					$message = "送信に失敗しました。送信先のメールアドレスが正しくありません";
				}
				catch(\EmailSendingFailedException $e)
				{
					//送信に失敗
					$message = "送信に失敗しました。";
				}
				//送信できた　try-catchでエラーなし。
				$message = '送信が完了しました。ありがとうございました。';
			}
			else
			{
				//フラッシュセッションが取得できないとき
				$message = 'お問い合わせフォームが正しく送信されていません。';
			}
			//メッセージを変数に渡す
			$data['message'] = $message;
			//ビューの読み込み
			$view = View::forge('contact/send',$data);
			return $view;
		}
 }
