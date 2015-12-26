<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class ReplyCreationForm extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

   public function rules() { 
   	    return[
	        'body'     => 'required|max:255',
        ];}
}
