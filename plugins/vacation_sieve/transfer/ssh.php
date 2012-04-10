<?php

# Use the interface
require 'sieve_transfer.php';

class SSHTransfer extends SieveTransfer
{
    public function LoadScript($path)
    {
        return file_get_contents($path);
    }

    public function SaveScript($path,$script)
    {
        $tmpFile = tempnam("/tmp", "sieve");
        file_put_contents($tmpFile,$script);

        $user = $this->params['user'];
        $host = $this->params['host'];

        if ( !$user ) $user = 'vmail';
        if ( !$host ) $host = 'localhost';

        # Copy the file
        $command = sprintf("scp '%s' %s@%s:%s", $tmpFile, $user, $host, $path);
        system($command);

        # Compile the file
        $command = sprintf("ssh %s@%s '/usr/bin/sievec \"%s\"'", $user, $host, $path);
        system($command);
        #print("$command");
        #exit;

        # Clean up
        unlink($tmpFile);

        return true;
    }
}
