<?php namespace Analytics\Syncing;

class Syncing {
    
    public function iterator($id) {
        
        $file = fopen("/var/www/html/server/app/storage/uploads/a4a1425a6fa10eb399759229f3553cfb6d98c3848ac5063af283190bbb802962.json", "r");
        //$file = fopen("/home/michael/Downloads/debug.json", "r");
        $i = 0;
        
        foreach($this->yielder($file, 'conversation_state', $id) as $content) {
            
            file_put_contents($i . ".json", $content);
            echo $i++ . PHP_EOL;
            
        }
        
        echo $i;
    }
    
    /**
     * This is not responsible for checking to see if you are using valid json.
     * That is your job.
     * 
     * Grab all data from a conversation.
     * May need to be broken down even farther....
     * This needs to be super refactored after it is working.
     */
    public function yielder($file, $element, $bytes = 1024) {
        
        // Hold the stream
        $stream = "";
        
        $totalBytes = 0;
        
        // Keep track of the number of objects that we have found.
        $objectNum = 0;
        
        // Make sure that even if the file has been read in we are not stoppping until
        // we have actually looked through all of the data.
        $arrayEnd = false;
        
        // Keep track if we have found the start of the array.
        // Based on the element that has been passed into $element.
        $foundArray = false;
        
        while(!feof($file)){
            
            $findElement = false;
            
            if(!$findElement) {
                $stream .= fread($file, $bytes);
                $totalBytes += 1024;
                
                $streamLength = mb_strlen($stream);
                // Check to make sure that we do not miss the string because it has been broken in half.
                if($streamLength > mb_strlen($element) && $streamLength > (3 * $bytes) && !$findElement) {
                    $stream = substr($stream, $bytes);
                }
                
            }
            
            $position = strpos($stream, $element);
            
            // Check if we have found the element we are looking for.
            // Then make sure it is an array...
            // Then start looping through all the objects it has...
            if($position !== false && !$foundArray) {
                // increment position so when checking for array we
                // do not run into trouble.
                $position++;
                // Hold current position we are looking for in array
                // for the $findArray array;
                $assertArray = 0;
                
                while(!feof($file)) {
                    $stream .= fread($file, $bytes);
                    $totalBytes += 1024;
                    
                    $streamLength = mb_strlen($stream);
                    
                    $findArray = [':', '['];
                    
                    for($i = $position; $i < $streamLength; $i++) {
                        
                        if(!ctype_space($stream{$i}) && $stream{$i} == $findArray[$assertArray] ) {
                            $assertArray++;
                        }
                        
                        if($assertArray == 2) {
                            // Hold onto the place that will be where
                            // the object starts.
                            $startArray = $i;
                            $foundArray = true;
                            // Leave the loop!
                            break;
                        }
                        
                    }
                    
                    if($assertArray == 2) {
                        break;
                    }
                }
                                
                if($foundArray) {
                
                    $openingBrackets = 0;
                    $closingBrackets = 0;
                    
                    // Place holder for object we are about to consume.
                    $object = substr($stream, ++$startArray);
                    
                    // Check if the last element in the string was [
                    // In that case set $object to empty string.
                    if($object === false) {
                        $object = "";
                    }
                    
                    $cursor = 0;
                    
                    // Keep track of when we are reading stuff that may be content.
                    $inString = false;
                    
                    // Keep track if the current char is being escaped.
                    $inEscape = false;
                    
                    while(!feof($file) || !$arrayEnd) {
                        
                        // Please save me.
                        fixme:
                        
                        $object .= fread($file, $bytes);
                        $totalBytes += 1024;
                        
                        echo $totalBytes / 1000000 . PHP_EOL;
                        
                        // Iterate over everything and find when we have read in
                        // an entire object from memory.
                        for(; $cursor < strlen($object); $cursor++) {
                            
                            if(!isset($object{$cursor})) $arrayEnd = true;
                            
                            // This hurts me more than it will ever hurt you.
                            omgplzstop:
                                
                            if($object{$cursor} == "\\") {
                                $cursor += 2;
                                if(!isset($object{$cursor})) goto fixme;
                                goto omgplzstop;
                            }
                            
                            if($object{$cursor} == '"') {
                                $inString = !$inString;
                            }
                            
                            if($object{$cursor} == "{" && !$inString) $openingBrackets++;
                            if($object{$cursor} == "}" && !$inString) $closingBrackets++;
                            
                            if($arrayEnd){
                                die("End of array");
                            }
                            
                            // Return object back for evaluation in loop.
                            if($openingBrackets == $closingBrackets && $openingBrackets != 0) {
                                
                                // Return result to calee function.
                                $objectNum++;
                                
                                yield trim(substr($object, 0, ++$cursor));
                                
                                $object = substr($object, $cursor);
                                
                                // At end of string create a new one
                                if($object === false) $object = "";
                                
                                // reset cursor. Moving it to where we left off
                                // Before we cut down the string size.
                                $cursor = 0;
                                
                                // Eat up array until next object is found.
                                while(!feof($file) || !$arrayEnd) {
                                    
                                    $object .= fread($file, $bytes);
                                    $totalBytes += 1024;
                                    
                                    for(; $cursor < strlen($object); $cursor++) {
                                        
                                        if($object{$cursor} == "{") {
                                            // Remove the old part of the object
                                            $object = substr($object, $cursor);
                                            $openingBrackets = 1;
                                            $closingBrackets = 0;
                                            $cursor++;
                                            
                                            goto fixme;
                                        } else if ($object{$cursor} == "]") {
                                            $arrayEnd = true;
                                        }
                                        
                                    }
                                    
                                }
                            }
                        }
                        
                    }
                    
                } else {
                    echo "The array of element {$element} was not found";
                }
                
            }
            
        }
        
        fclose($file);
        
    }
    
}

(new Syncing)->iterator(1024);
