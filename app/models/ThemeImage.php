<?php

class ThemeImage extends \Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 't_theme_images';
	
	public $timestamps = false;

	// Don't forget to fill this array
	protected $fillable = ['name', 'image', 'ordering', 'state', 'theme_id'];
	
	/**
	* Relationship with table t_themes
	*/
	public function theme() {
		return $this->belongsTo('Theme');
	}

}