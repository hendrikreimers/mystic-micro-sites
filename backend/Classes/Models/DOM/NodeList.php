<?php
declare(strict_types=1);

namespace Models\DOM;

/**
 * Iterable list of nodes
 *
 */
class NodeList implements \Iterator, \Countable {
  /**
   * @var array
   */
  private array $nodes = [];

  /**
   * @var int
   */
  private int $position = 0;

  /**
   * @param array $nodes
   */
  public function __construct(array $nodes = []) {
    $this->nodes = $nodes;
  }

  /**
   * Add Node to list
   *
   * @param DOMNode $node
   * @return void
   */
  public function addNode(DOMNode $node): void {
    $this->nodes[] = $node;
  }

  /**
   * Count of NodeList Nodes
   *
   * @return int
   */
  public function count(): int {
    return count($this->nodes);
  }

  /**
   * Returns current node
   *
   * @return DOMNode
   */
  public function current(): DOMNode {
    return $this->nodes[$this->position];
  }

  /**
   * Returns current index position
   *
   * @return int
   */
  public function key(): int {
    return $this->position;
  }

  /**
   * Next index set
   *
   * @return void
   */
  public function next(): void {
    ++$this->position;
  }

  /**
   * Reset position
   *
   * @return void
   */
  public function rewind(): void {
    $this->position = 0;
  }

  /**
   * Returns true if position exists and key set
   *
   * @return bool
   */
  public function valid(): bool {
    return isset($this->nodes[$this->position]);
  }

  /**
   * Replaces a node by given position
   *
   * @param int $index
   * @param DOMNode $replacement
   * @return void
   */
  public function replaceByIndex(int $index, DOMNode $replacement): void {
    $this->nodes[$index] = $replacement;
  }
}
