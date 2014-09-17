<?php namespace Analytics\Syncing;

class Syncing {
    
    public function iterator($id) {
        
        $file = fopen("/var/www/html/server/app/storage/uploads/a4a1425a6fa10eb399759229f3553cfb6d98c3848ac5063af283190bbb802962.json", "r");
        
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
        
        $stream = "";
        
        // Keep track if we have found the start of the array.
        // Based on the element that has been passed into $element.
        $foundArray = false;
        
        while(!feof($file)){
            
            $findElement = false;
            
            if(!$findElement) {
                $stream .= fread($file, $bytes);
                
                $streamLength = mb_strlen($stream);
                // Check to make sure that we do not miss the string because it has been broken in half.
                if($streamLength > mb_strlen($element) && $streamLength > (3 * $bytes) && !$findElement) {
                    $stream = substr($stream, $bytes);
                }
                
            } else {
                
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
                
                while(!feof($file)){
                    $stream .= fread($file, $bytes);
                    
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
                    } else {
                        // TODO: Reimpliment this case.
                    }
                    
                    $cursor = 0;
                    
                    $debug = false;
                    
                    while(!feof($file)) {
                        
                        // Please save me.
                        fixme:
                        
                        $object .= fread($file, $bytes);
                        
                        // Iterate over everything and find when we have read in
                        // an entire object from memory.
                        for(; $cursor < strlen($object); $cursor++) {
                            
                            if($object{$cursor} == "{") $openingBrackets++;
                            if($object{$cursor} == "}") $closingBrackets++;
                            
                            // Return object back for evaluation in loop.
                            if($openingBrackets == $closingBrackets && $openingBrackets != 0) {
                                // Return result to calee function.
                                yield trim(substr($object, 0, ++$cursor));
                                
                                $object = substr($object, $cursor);
                                
                                // At end of string create a new one
                                if($object === false) $object = "";
                                
                                // reset cursor.
                                $cursor = 0;
                                
                                // Eat up array until next object is found.
                                while(!feof($file)) {
                                    $object .= fread($file, $bytes);
                                    
                                    for(; $cursor < strlen($object); $cursor++) {
                                        if($object{$cursor} == "{") {
                                            $object = substr($object, $cursor);
                                            $openingBrackets = 1;
                                            $closingBrackets = 0;
                                            $cursor++;
                                            
                                            $debug = true;
                                            
                                            goto fixme;
                                        } else if ($object{$cursor} == "]") {
                                            die("end of array!");
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
