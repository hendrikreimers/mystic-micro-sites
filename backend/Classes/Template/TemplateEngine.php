<?php

declare(strict_types=1);

namespace Template;

/**
 * Class TemplateEngine
 *
 * A class responsible for rendering templates with dynamic content
 * using a simple syntax for variable replacement and tag processing.
 */
class TemplateEngine
{
  /**
   * @var string The content of the template file.
   */
  private string $template;

  /**
   * @var TemplateView An instance of the TemplateView class for managing template variables.
   */
  public TemplateView $view;

  /**
   * TemplateEngine constructor.
   *
   * Initializes the template engine with a given template file path.
   *
   * @param string $templateFilePath The file path to the template.
   */
  public function __construct(string $templateFilePath)
  {
    // Load the template file content
    $this->template = file_get_contents($templateFilePath);
    // Initialize a new TemplateView instance
    $this->view = new TemplateView();
  }

  /**
   * Render the template with variables and custom tags replaced.
   *
   * @return string The rendered template as a string.
   */
  public function render(): string
  {
    // Replace variables in the template with actual values
    $parsedTemplate = $this->replaceVariables($this->template);
    // Parse custom tags and return the final HTML
    return $this->parse($parsedTemplate);
  }

  /**
   * Replace template variables within {{ ... }} with their values.
   *
   * @param string $html The template content to process.
   * @return string The template with variables replaced.
   */
  private function replaceVariables(string $html): string
  {
    // Use regex to find all {{ variable }} patterns
    return preg_replace_callback('/{{\s*([\w.\[\d\]]+)\s*([|]{0,}\s*)([\w.]{0,})(\s*)?}}/', function ($matches) {
      // Extract the variable name and optional function pipe
      $variableName = $matches[1];
      $variablePipe = $matches[3];

      // Retrieve the variable value from the view
      $value = $this->view->getNested($variableName);

      // Use the variable's value or fallback to the matched pattern if not found
      $returnValue = $value !== null ? $value : $matches[0];

      // Apply a function to the value if a pipe is specified
      return ($variablePipe) ? $variablePipe($returnValue) : $returnValue;
    }, $html);
  }

  /**
   * Check if a given string is a valid JSON string.
   *
   * @param string $string The string to check.
   * @return bool True if the string is valid JSON, false otherwise.
   */
  private function isJson(string $string): bool
  {
    // Decode the string and check for JSON errors
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
  }

  /**
   * Parse custom tags within the template.
   *
   * @param string $html The template content to process.
   * @return string The HTML content with custom tags processed.
   */
  private function parse(string $html): string
  {
    // Define a regex pattern to match <mms:...> tags
    $pattern = '/<mms:([\w]+)([^>]*)>(.*?)<\/mms:\1>/s';

    // Process each tag iteratively, starting with the outermost
    while (preg_match($pattern, $html, $matches)) {
      // Process the matched tag and get the rendered content
      $renderedContent = trim($this->processTag($matches));

      // Replace the matched tag with its rendered content
      $html = str_replace($matches[0], $renderedContent, $html);
    }

    return $html;
  }

  /**
   * Process an individual custom tag.
   *
   * @param array $matches An array of matches from the regex pattern.
   * @return string The processed content for the tag.
   * @throws \Exception If the class or render method does not exist.
   */
  private function processTag(array $matches): string
  {
    // Extract matches for class name, attributes, and inner content
    [$fullMatch, $className, $attributesString, $innerContent] = $matches;

    // Convert the attributes string to an associative array
    $attributes = $this->parseAttributes($attributesString);

    // Create a fully qualified class name for the view helper
    $className = '\\Template\\ViewHelper\\' . ucfirst($className) . 'ViewHelper';

    // Check if the specified class exists
    if (!class_exists($className)) {
      throw new \Exception("Class $className not found");
    }

    // Instantiate the view helper class
    $instance = new $className();

    // Ensure the class has a render method
    if (!method_exists($instance, 'render')) {
      throw new \Exception("Class $className does not have a render method");
    }

    // Pass the TemplateView instance to the view helper if it has a 'view' property
    if (property_exists($instance, 'view')) {
      $instance->view = $this->view;
    }

    // Render the tag using the attributes and inner content
    return $instance->render($attributes, trim($innerContent));
  }

  /**
   * Parse the attributes string into an associative array.
   *
   * @param string $attributesString The raw string of attributes.
   * @return array An associative array of attribute names and values.
   */
  private function parseAttributes(string $attributesString): array
  {
    $attributes = [];
    // Regex pattern to match attribute key-value pairs
    $pattern = '/([\w\-]+)="([^"]*)"/';

    // Find all attribute matches
    preg_match_all($pattern, $attributesString, $matches, PREG_SET_ORDER);

    // Process each attribute match
    foreach ($matches as $match) {
      // Replace variables within attribute values
      $value = preg_replace_callback('/{{\s*([\w.\[\d\]]+)\s*([|]{0,}\s*)([\w.]{0,})(\s*)?}}/', function ($varMatch) {
        // Resolve the variable name to its value
        $resolvedValue = $this->view->getNested($varMatch[1]);
        return $resolvedValue !== null ? $resolvedValue : $varMatch[0];
      }, $match[2]);

      // Decode the value if it is a JSON string
      if ($this->isJson($value)) {
        $value = json_decode($value, true);
      }

      // Store the attribute and its value
      $attributes[$match[1]] = $value;
    }
    return $attributes;
  }
}
