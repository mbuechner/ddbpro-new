<?php

namespace Drupal\ddbp_tools;

use DateTime;
use Spatie\CalendarLinks\Link;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\calendar_link\CalendarLinkException;
use Spatie\CalendarLinks\Exceptions\InvalidLink;
use Drupal\calendar_link\Twig\CalendarLinkTwigExtension;

/**
 * Class OriginalServiceOverride
 * Used for altering the time in generated .ics files because they have UTC as default timezone.
 */
class CalendarLinkTwigExtensionOverride extends CalendarLinkTwigExtension {
  /**
   * Original service object.
   *
   * @var \Drupal\calendar_link\Twig\CalendarLinkTwigExtension
   */
  protected $originalService;

  /**
   * CalendarLinkTwigExtension constructor.
   *
   * @param \Drupal\calendar_link\Twig\CalendarLinkTwigExtension $original_service
   *   Original Extension.
   */
  public function __construct(CalendarLinkTwigExtension $original_service) {
    $this->originalService = $original_service;
  }

  /**
   * Create a calendar link.
   *
   * All data parameters accept multiple types of data and will attempt to get
   * the relevant information from e.g. field instances or content arrays.
   *
   * @param string $type
   *   Generator key to use for link building.
   * @param mixed $title
   *   Calendar entry title.
   * @param mixed $from
   *   Calendar entry start date and time.
   * @param mixed $to
   *   Calendar entry end date and time.
   * @param mixed $all_day
   *   Indicator for an "all day" calendar entry.
   * @param mixed $description
   *   Calendar entry description.
   * @param mixed $address
   *   Calendar entry address.
   *
   * @return string
   *   URL for the specific calendar type.
   */
  public function calendarLink(string $type, $title, $from, $to, $all_day = FALSE, $description = '', $address = ''): string {
    if (!isset(self::$types[$type])) {
      throw new CalendarLinkException('Invalid calendar link type.');
    }

    try {
      $link = Link::create(
        $this->getString($title),
        $this->getDateTime($from),
        $this->getDateTime($to),
        $this->getBoolean($all_day)
      );
    }
    catch (InvalidLink $e) {
      throw new CalendarLinkException('Invalid calendar link data.');
    }

    if ($description) {
      $link->description($this->getString($description));
    }

    if ($address) {
      $link->address($this->getString($address));
    }

    return $link->{$type}();
  }

  /**
   * Gets a PHP \DateTime instance from various types of input.
   *
   * @param mixed $date
   *   A value with \DateTime data.
   *
   * @return \DateTime
   *   The \DateTime instance.
   *
   * @throws \Drupal\calendar_link\CalendarLinkException
   */
  protected function getDateTime($date): DateTime {
    if ($date instanceof DrupalDateTime) {
      // Subtract 2 hrs to get from UTC to UTC+2 because setting timezone is not affecting generated ics.
      $date->modify('-2 hours');
      return $date->getPhpDateTime();
    }

    throw new CalendarLinkException('Could not get date and time from input value ' . $date . ' (' . get_class($date) . ').');
  }

  /**
   * Gets a string value from various types of input.
   *
   * @param mixed $data
   *   A value with a string.
   *
   * @return string
   *   String from data.
   *
   * @throws \Drupal\calendar_link\CalendarLinkException
   */
  private function getString($data): string {
    // Content field array. E.g. `label`.
    if (is_array($data)) {
      if (isset($data['#items'])) {
        $data = $data['#items'];
      }
      else {
        // If not "#items" key is present the field is empty.
        return '';
      }
    }

    // Drupal field instance. E.g. `node.title`.
    if ($data instanceof FieldItemListInterface) {
      $data = $data->getString();
    }

    // Other common object string representation methods, if available.
    if (is_object($data)) {
      if (method_exists($data, '__toString')) {
        $data = (string) $data;
      }
      elseif (method_exists($data, 'toString')) {
        $data = $data->toString();
      }
    }

    if (is_string($data)) {
      return $data;
    }

    throw new CalendarLinkException('Could not get string from input type ' . get_class($data) . '.');
  }

  /**
   * Gets a boolean value from various types of input.
   *
   * @param mixed $data
   *   A value with a boolean value.
   *
   * @return bool
   *   Boolean from data.
   *
   * @throws \Drupal\calendar_link\CalendarLinkException
   */
  private function getBoolean($data): bool {
    if (is_bool($data)) {
      return $data;
    }

    try {
      $data = $this->getString($data);
      return (bool) $data;
    }
    catch (CalendarLinkException $e) {
      throw new CalendarLinkException('Could not get valid boolean from input.');
    }
  }

}
