<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectInput extends Component
{

    public $name;
    public $options;
    public $selected;
    public $disabled;


    /**
     * Create a new component instance.
     */
    public function __construct($name, $options = [], $selected = null, $disabled = false)
    {
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
        $this->disabled = $disabled;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-input');
    }
}
