<?php
namespace Blocks;

/**
 * Username model
 */
class UsernameModel extends BaseModel
{
	public function defineAttributes()
	{
		return array(
			'username' => array(AttributeType::String, 'required' => true),
		);
	}
}
