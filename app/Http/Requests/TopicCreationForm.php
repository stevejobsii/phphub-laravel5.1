<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class TopicCreationForm extends Request 
{

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
        'title'   => 'required|min:2',
        'body'    => 'required|min:2',
        'node_id' => 'required|numeric'
    ];}
}
