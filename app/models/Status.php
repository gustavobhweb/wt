<?php

class Status extends Eloquent
{

    protected $table = 'status';

    /**
     *
     * @var array
     *
     */
    protected $fillable = [
        'titulo'
    ];
}