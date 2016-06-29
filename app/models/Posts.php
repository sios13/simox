<?php

use Simox\Model;

class Posts extends Model
{
    public $id;
    public $title;
	public $title_slug;
    public $category;
    public $text;
	public $date;
}
