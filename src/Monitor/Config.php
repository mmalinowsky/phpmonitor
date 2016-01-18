<?php
namespace Monitor;

class Config
{
    private $data = [];

    public function __construct()
    {
        //notification services
        $this->data['notification'] = [
            'data'     => ['mail_to' => 'admin@localhost'],
            'services' => ['Mail\FakeSimple', 'Mail\FakeSmtp']
         ];
        //time(in hours) after next notification with same trigger type and for same server could be sent
        $this->data['notification_delay_in_hours'] = 1;
        //time (in days) after server history old records will be deleted
        $this->data['history_expire_time_in_days'] = 7;

        $this->data['hostname'] = 'localhost';
        $this->data['username'] = '';
        $this->data['password'] = '';
        $this->data['database'] = '';
        $this->data['dbdriver'] = 'mysql';
    }

    public function get($name)
    {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }
}