<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';
require_once 'producttemplate.chromaticity.class.php';
require_once 'producttemplate.machine.class.php';
require_once 'producttemplate.paper.class.php';
require_once 'producttemplate.productformat.class.php';

class Producttemplate extends Model{
    public $_table = 'producttemplates';

    public $name = '';
    public $description = '';
    public $picture = 0;
    public $contents = 1;
    public $envelope = 0;
    public $envelope_pages_from = 1;
    public $envelope_pages_to = 1;
    public $envelope_pages_interval = 1;
    public $envelope_factor_width = 1.0;
    public $envelope_factor_height = 1.0;

    public $uptdate = 0;
    public $uptuser;

    public $tradegroup;

    protected function bootClasses()
    {
        $this->tradegroup = new Tradegroup($this->tradegroup);
        $this->uptuser = new User($this->uptuser);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return int
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param int $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return int
     */
    public function getEnvelope()
    {
        return $this->envelope;
    }

    /**
     * @param int $envelope
     */
    public function setEnvelope($envelope)
    {
        $this->envelope = $envelope;
    }

    /**
     * @return int
     */
    public function getEnvelopePagesFrom()
    {
        return $this->envelope_pages_from;
    }

    /**
     * @param int $envelope_pages_from
     */
    public function setEnvelopePagesFrom($envelope_pages_from)
    {
        $this->envelope_pages_from = $envelope_pages_from;
    }

    /**
     * @return int
     */
    public function getEnvelopePagesTo()
    {
        return $this->envelope_pages_to;
    }

    /**
     * @param int $envelope_pages_to
     */
    public function setEnvelopePagesTo($envelope_pages_to)
    {
        $this->envelope_pages_to = $envelope_pages_to;
    }

    /**
     * @return int
     */
    public function getEnvelopePagesInterval()
    {
        return $this->envelope_pages_interval;
    }

    /**
     * @param int $envelope_pages_interval
     */
    public function setEnvelopePagesInterval($envelope_pages_interval)
    {
        $this->envelope_pages_interval = $envelope_pages_interval;
    }

    /**
     * @return float
     */
    public function getEnvelopeFactorWidth()
    {
        return $this->envelope_factor_width;
    }

    /**
     * @param float $envelope_factor_width
     */
    public function setEnvelopeFactorWidth($envelope_factor_width)
    {
        $this->envelope_factor_width = $envelope_factor_width;
    }

    /**
     * @return float
     */
    public function getEnvelopeFactorHeight()
    {
        return $this->envelope_factor_height;
    }

    /**
     * @param float $envelope_factor_height
     */
    public function setEnvelopeFactorHeight($envelope_factor_height)
    {
        $this->envelope_factor_height = $envelope_factor_height;
    }

    /**
     * @return Tradegroup
     */
    public function getTradegroup()
    {
        return $this->tradegroup;
    }

    /**
     * @param Tradegroup $tradegroup
     */
    public function setTradegroup($tradegroup)
    {
        $this->tradegroup = $tradegroup;
    }

    /**
     * @return int
     */
    public function getUptdate()
    {
        return $this->uptdate;
    }

    /**
     * @param int $uptdate
     */
    public function setUptdate($uptdate)
    {
        $this->uptdate = $uptdate;
    }

    /**
     * @return User
     */
    public function getUptuser()
    {
        return $this->uptuser;
    }

    /**
     * @param User $uptuser
     */
    public function setUptuser($uptuser)
    {
        $this->uptuser = $uptuser;
    }
}