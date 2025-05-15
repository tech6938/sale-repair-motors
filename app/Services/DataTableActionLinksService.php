<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DataTableActionLinksService
 * 
 * This service is a helper service to generate action links' HTML for the yajra datatable
 * 
 * @author Taimoor Ali Khan (saytaimoor@gmail.com)
 * @version 1.2
 * @package App\Services
 */
class DataTableActionLinksService
{
    /**
     * The available CRUD links
     */
    private const CRUD_LINKS = [
        ['action' => 'update'],
        ['action' => 'view'],
        ['action' => 'delete'],
    ];

    /**
     * Generated HTML to send in response
     */
    private $html = '';

    /**
     * Initialize the service
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @param string $routeNamespace
     * @param string $datatableId
     * @param string $isLocked
     * @param array $additionalRouteParams
     */
    public function __construct(
        private Model $model,
        private string $routeNamespace,
        private string $datatableId = '',
        private bool $isLocked = false,
        private array $additionalRouteParams = [],
    ) {}

    /**
     * Generate all HTML links related to CRUD functionality.
     * Optionally an extra array can also be provided to override certain links.
     * 
     * @param $overrideLinks
     * @return string
     */
    public function crud(array $overrideLinks = []): string
    {
        return $this->byArray(
            !empty($overrideLinks)
                ? $this->overrideDefaultLinks(self::CRUD_LINKS, $overrideLinks)
                : self::CRUD_LINKS
        );
    }

    /**
     * Generate all HTML links related to CRUD functionality 
     * other then the provided actions.
     * Optionally an extra array can also be provided to override certain links.
     * 
     * @param array $excludedLinks
     * @param array $overrideLinks
     * @return string
     */
    public function except(array $excludedLinks, array $overrideLinks = []): string
    {
        $filteredLinks = array_filter(self::CRUD_LINKS, function ($link) use ($excludedLinks) {
            return !in_array($link['action'], $excludedLinks);
        });

        return $this->byArray(
            !empty($overrideLinks)
                ? $this->overrideDefaultLinks($filteredLinks, $overrideLinks)
                : $filteredLinks
        );
    }

    /**
     * Generate all HTML links related to CRUD functionality 
     * but only those action which are provided.
     * Optionally an extra array can also be provided to override certain links.
     * 
     * @param array $includedLinks
     * @param array $overrideLinks
     * @return string
     */
    public function only(array $includedLinks, array $overrideLinks = []): string
    {
        $filteredLinks = array_filter(self::CRUD_LINKS, function ($link) use ($includedLinks) {
            return in_array($link['action'], $includedLinks);
        });

        return $this->byArray(
            !empty($overrideLinks)
                ? $this->overrideDefaultLinks($filteredLinks, $overrideLinks)
                : $filteredLinks
        );
    }

    /**
     * Generate HTML links by array
     * 
     * @param array $link
     * @return string
     */
    public function byArray(array $links)
    {
        // Return lock HTML if isLocked attribute is available
        if ($this->isLocked) return $this->lockHtml();

        // Generate the links HTML
        $this->generateHtml($links);

        // Return lock HTML if generated HTML doesn't contain anything
        if (empty($this->html)) return $this->lockHtml();

        return sprintf('
            <ul class="nk-tb-actions gx-1">
                <li>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                            <em class="icon ni ni-more-h"></em>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right"><ul class="link-list-opt no-bdr">%s</ul></div>
                    </div>
                </li>
            </ul>
        ', $this->html);
    }

    /**
     * Return the parameters list for the resource routes
     * 
     * @return array
     */
    private function getRouteParams(): array
    {
        return array_merge($this->additionalRouteParams, [$this->model?->uuid]);
    }

    /**
     * Override the default one or more links with the provided links
     * 
     * @param array $defaultLinks
     * @param array $overrideLinks
     * @return array
     */
    private function overrideDefaultLinks(array $defaultLinks, array $overrideLinks): array
    {
        if (is_array($overrideLinks) && !empty($overrideLinks)) {
            foreach ($defaultLinks as &$defaultLink) {
                if (!isset($defaultLink['action'])) continue;

                foreach ($overrideLinks as $newItem) {
                    // Null-safe check for malformed override links
                    if (is_array($newItem) && isset($newItem['action']) && $defaultLink['action'] === $newItem['action']) {
                        $defaultLink = array_merge($defaultLink, $newItem);
                    }
                }
            }

            unset($defaultLink); // break reference after loop
        }

        return $defaultLinks;
    }

    /**
     * Return the lock HTML
     * 
     * @return string
     */
    private function lockHtml()
    {
        return '
            <ul class="nk-tb-actions gx-1">
                <li>
                    <a href="javascript:void(0);" class="btn btn-icon btn-trigger">
                        <em class="icon ni ni-lock-alt"></em>
                    </a>
                </li>
            </ul>
        ';
    }

    /**
     * Compile the HTML links using the array of links
     * 
     * @param array $links
     * @return void
     */
    private function generateHtml(array $links): void
    {
        foreach ($links as $link) {
            if (!$this->isValidLink($link)) {
                throw new \Exception("Provided link action ({$link['action']}) is invalid.");
            }

            if ($link['action'] == 'update') $this->generateUpdateLink($link);
            if ($link['action'] == 'view') $this->generateViewLink($link);
            if ($link['action'] == 'delete') $this->generateDeleteLink($link);
            if ($link['action'] == 'custom') $this->generateCustomLink($link);
        }
    }

    /**
     * Check if the provided link is a valid link
     * 
     * @param array $link
     * @return bool
     */
    private function isValidLink(array $link): bool
    {
        return isset($link['action']) && in_array($link['action'], ['update', 'view', 'delete', 'custom']);
    }

    /**
     * Extract and return attributes from a link
     * 
     * @param array $link
     * @return string
     */
    private function getAttributes(array $link): string
    {
        // get custom attributes
        $attributes = array_key_exists('attributes', $link) ? $link['attributes'] : '';

        // append relevant attributes in case of sync response
        $attributes .= array_key_exists('syncResponse', $link) && $link['syncResponse'] ? '' : ' async-modal';

        // change Bootstrap modal size based on the provided parameter
        $attributes .= array_key_exists('modalSize', $link) && !empty($link['modalSize'])
            ? " async-modal-size='{$link['modalSize']}'"
            : '';

        return $attributes;
    }

    /**
     * Generate HTML for update Link
     * 
     * @return void
     */
    private function generateUpdateLink(array $link): void
    {
        if (!Route::has("{$this->routeNamespace}.edit")) return;

        if (array_key_exists('shouldRender', $link) && !$link['shouldRender']) return;

        $this->html .= sprintf(
            '<li>
                <a href="%s" %s>
                    <em class="icon ni ni-edit"></em>
                    <span>Update</span>
                </a>
            </li>',
            route("{$this->routeNamespace}.edit", $this->getRouteParams()),
            $this->getAttributes($link)
        );
    }

    /**
     * Generate HTML for view Link
     * 
     * @param array $link
     * @return void
     */
    private function generateViewLink(array $link): void
    {
        if (!Route::has("{$this->routeNamespace}.show")) return;

        if (array_key_exists('shouldRender', $link) && !$link['shouldRender']) return;

        $this->html .= sprintf(
            '<li>
                <a href="%s" %s>
                    <em class="icon ni ni-eye"></em>
                    <span>View Details</span>
                </a>
            </li>',
            route("{$this->routeNamespace}.show", $this->getRouteParams()),
            $this->getAttributes($link)
        );
    }

    /**
     * Generate HTML for delete Link
     * 
     * @return void
     */
    private function generateDeleteLink(array $link): void
    {
        if (!Route::has("{$this->routeNamespace}.destroy")) return;

        if (array_key_exists('shouldRender', $link) && !$link['shouldRender']) return;

        // Add this to avoid 'async-modal' attribute being added
        $link['syncResponse'] = true;

        $this->html .= sprintf(
            '<li>
                <a href="%s" class="text-danger delete" delete-btn %s %s>
                    <em class="icon ni ni-trash"></em>
                    <span>Delete</span>
                </a>
            </li>',
            route("{$this->routeNamespace}.destroy", $this->getRouteParams()),
            $this->datatableId ? "data-datatable='{$this->datatableId}'" : '',
            $this->getAttributes($link)
        );
    }

    /**
     * Generate custom HTML link
     * 
     * @param array $link
     * @return void
     */
    private function generateCustomLink(array $link): void
    {
        // Check for required fields
        if (
            !array_key_exists('url', $link) ||
            !array_key_exists('icon', $link) ||
            !array_key_exists('buttonText', $link)
        ) {
            throw new \Exception('"url", "icon" and "buttonText" is required for custom link.');
        }

        if (array_key_exists('shouldRender', $link) && !$link['shouldRender']) return;

        $this->html .= sprintf(
            '<li>
                <a href="%s" %s>
                    <em class="icon ni ni-%s"></em>
                    <span>%s</span>
                </a>
            </li>',
            $link['url'],
            $this->getAttributes($link),
            $link['icon'],
            $link['buttonText'],
        );
    }
}
