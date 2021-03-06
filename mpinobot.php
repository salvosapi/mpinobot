<?php 
//$this->config contains configuration and bot Information
//$this->data contains message and sender information
class mpinobot
{
	private $config;
	private $commands;
	private $data;
	private $response;
	private $url;

	function __construct()
	{
		$this->config = json_decode(file_get_contents('.config'), true);
		$this->commands = json_decode(file_get_contents('.commands'), true);

    fwrite(fopen('log.txt', 'w'), print_r($this->commands, true));

		$this->url = "https://api.telegram.org/bot".$this->config['175716891:AAFvRodZFRvshS5vjyvnPTRoHQis45wthB8'];

		if($this->config['mpinobot']=='' || $this->config['Mpino']=='')
		{
			$p = json_decode(file_get_contents($this->url."/getme"),true);
			$this->config['mpinobot'] = $p['result']['mpinobot'];
			$this->config['Mpino'] = $p['result']['Mpino'];
		}

		$this->data = json_decode(file_get_contents("php://input"),true);

		if($this->config['wl_enable']==1)
		{
		  if(array_key_exists('new_chat_participant', $this->data['message']) && $this->data['message']['new_chat_participant']['username']!=$this->config['username']) { $this->welcome(); }
		}

		$this->process();
		$this->respond();
		$this->response = null;

		var_dump($this->config);

	}

    private function welcome()
	{
		$this->response = "@".$this->data['message']['new_chat_participant']['username']." ".$this->config['wl_message'];
		$this->respond();
		$this->response = null;
	}

	private function process()
	{
		foreach($this->commands as $command=>$response)
		{
			if($this->data['message']['text'] == $command || $this->data['message']['text'] == $command."@".$this->config['username'] || $this->data['message']['text'] == $command." @".$this->config['username']){ $this->response = $response; }
		}
	}

	private function respond()
	{
		file_get_contents($this->url."/sendMessage?chat_id=".$this->data['message']['chat']['id']."&text=".urlencode($this->response));
	}
}
