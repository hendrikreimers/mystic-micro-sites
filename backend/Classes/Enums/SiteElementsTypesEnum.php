<?php
declare(strict_types=1);

namespace Enums;

enum SiteElementsTypesEnum: string {
  case Headline = 'headline';
  case Text = 'text';
  case Image = 'image';
  case Link = 'link';
  case VCard = 'vcard';
}
