<?php

namespace LogEntriesEtc;

class Config
{
    public function getBaseURL()
    {
        return plugin_dir_url(__FILE__);
    }
}
