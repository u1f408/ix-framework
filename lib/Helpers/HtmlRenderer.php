<?php
declare(strict_types=1);
namespace ix\Helpers;

use ix\HookMachine;
use ix\Helpers\ArrayHelpers;

class HtmlRenderer {
	/**
	 * @param mixed[] $args
	 */
	public function render(...$args): string {
		return implode("", array_map('strval', ArrayHelpers::array_flatten($args)));
	}

	/**
	 * @param string $tag
	 * @param array<string, mixed> $attributes
	 * @return string
	 */
	public function tag(string $tag, array $attributes): string {
		$escapedAttributes = "";
		foreach ($attributes as $name => $value) {
			$value = htmlspecialchars(trim(strval($value)), ENT_QUOTES);
			$escapedAttributes .= " {$name}=\"{$value}\"";
		}

		return "<{$tag}{$escapedAttributes}>";
	}

	/**
	 * @param string $tag
	 * @param array<string, mixed> $attributes
	 * @param mixed[] $children
	 * @return string
	 */
	public function tagHasChildren(string $tag, array $attributes, ...$children): string {
		$escapedAttributes = "";
		foreach ($attributes as $name => $value) {
			$value = htmlspecialchars(trim(strval($value)), ENT_QUOTES);
			$escapedAttributes .= " {$name}=\"{$value}\"";
		}

		$children = $this->render(...$children);
		return "<{$tag}{$escapedAttributes}>{$children}</{$tag}>";
	}
	
	/**
	 * @param mixed[] $head
	 * @param mixed[] $body
	 * @param array<string, mixed> $htmlAttributes
	 * @param array<string, mixed> $bodyAttributes
	 * @param string $doctype
	 */
	public function renderDocument(
		array $head,
		array $body,
		array $htmlAttributes = [],
		array $bodyAttributes = [],
		string $doctype = '<!DOCTYPE html>'
	): string {
		// Execute renderDocument hooks
		list($_, $htmlAttributes, $head, $bodyAttributes, $body, $doctype) =
			HookMachine::execute(
				[self::class, 'renderDocument'],
				[$this, $htmlAttributes, $head, $bodyAttributes, $body, $doctype],
			);

		// Return rendered HTML
		return $this->render(...[
			$doctype,
			$this->tagHasChildren('html', $htmlAttributes, ...[
				$this->tagHasChildren('head', [], $head),
				$this->tagHasChildren('body', $bodyAttributes, $body),
			]),
		]);
	}
}
