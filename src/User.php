<?php

namespace Aiddroid\Social;

use Aiddroid\Social\Interfaces\UserInterface;

/**
 * Class User.
 */
class User implements UserInterface, \ArrayAccess, \JsonSerializable
{
    /**
     * User id.
     *
     * @var
     */
    protected $id;

    /**
     * User nickname.
     *
     * @var
     */
    protected $nickname;

    /**
     * User avatar url.
     *
     * @var string
     */
    protected $avatar;

    /**
     * User profile.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * User constructor.
     *
     * @param $id
     * @param $nickname
     * @param string $avatar
     * @param array  $attributes
     */
    public function __construct($id, $nickname, $avatar = '', $attributes = [])
    {
        $this->id = $id;
        $this->nickname = $nickname;
        $this->avatar = $avatar;
        $this->attributes = $attributes;
    }

    /**
     * Whether a offset exists.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return bool true on success or false on failure.
     *              </p>
     *              <p>
     *              The return value will be casted to boolean if non-boolean was returned.
     *
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Offset to retrieve.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     *
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
    }

    /**
     * Offset to set.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     *
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Offset to unset.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     *
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Get user id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get nickname.
     *
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Get avatar url.
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id'         => $this->id,
            'nickname'   => $this->nickname,
            'avatar'     => $this->avatar,
            'attributes' => $this->attributes,
        ];
    }
}
