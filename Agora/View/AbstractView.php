<?php

namespace Agora\View;

abstract class AbstractView
{
    private $model;         // The model associated with this view
    private $template;      // The template file to be used for rendering
    private $fields = [];   // An associative array to hold template fields

    // Constructor to initialize the view with a model and template
    public function __construct($model = null, $template = null)
    {
        $this->model = $model;
        $this->template = $template;
    }

    // Getter for the model
    public function getModel()
    {
        return $this->model;
    }

    // Setter for the model
    public function setModel($model): void
    {
        $this->model = $model;
    }

    // Setter for the template file
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    // Method to set a single template field
    public function setTemplateField(string $name, $value): void
    {
        $this->fields[$name] = $value;
    }
    public function getTemplateField(string $key)
    {
        return $this->templateFields[$key] ?? null; // Return null if the key doesn't exist
    }

    // Method to set multiple template fields
    public function setTemplateFields(array $fields): void
    {
        $this->fields = array_merge($this->fields, $fields);
    }

    // Method to render the view
    public function render(): string
    {
        // Prepare data for rendering
        $this->prepare();

        // Extract fields for use in the template
        extract($this->fields);

        // Start output buffering
        ob_start();

        // Include the template file
        if (file_exists($this->template)) {
            include $this->template;
        } else {
            throw new \Exception("Template file not found: " . $this->template);
        }

        // Return the rendered content
        return ob_get_clean();
    }

    // Method to prepare data for rendering (can be overridden in subclasses)
    public function prepare(): void
    {
        // Default implementation can be empty; subclasses can provide specific logic
    }
}