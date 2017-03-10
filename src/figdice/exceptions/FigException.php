<?php

namespace figdice\exceptions;

class FigException extends \Exception
{
    public function setTagFileLine($tag, $filename, $line)
    {
        $this->tag = $tag;
        $this->file = $filename;
        $this->line = $line;
    }
}
