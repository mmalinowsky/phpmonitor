<?php
namespace Monitor\Notification;

class Parser
{

    /**
     * Parse notification message
     *
     * @param  notification $notification
     * @param  array        $data
     * @return
     */
    public function parse(Notification &$notification, array $data)
    {
        $message = $notification->getMessageTemplate();
        //get all words in {} brackets
        preg_match_all("/{(\w+)}/", $message, $vars);
        foreach ($vars[1] as $var) {
            //if $data has key named as previous fetched word we replace it with $data value
            if (isset($data[$var])) {
                $message = preg_replace("{{" . $var . "}}", $data[$var], $message);
            }
        }
        $notification->setMessage($message);
    }
}
