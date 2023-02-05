<?php

namespace App\Services;

class DashboardMutuService
{
    private $title,
        $label,
        $val;

    public function result()
    {
        return collect([
            "title" => $this->title,
            "label" => $this->label,
            "val" => $this->val,
        ]);
    }

    /**
     * Set the value of label
     *
     * @return  self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the value of val
     *
     * @return  self
     */
    public function setVal($val)
    {
        $this->val = $val;

        return $this;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
