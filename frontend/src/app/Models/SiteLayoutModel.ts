import {
  FontFamilies, SiteElementHeadlineInterface, SiteElementImageInterface,
  SiteElementInterface,
  SiteElementLinkInterface, SiteElements, SiteElementsTypes, SiteElementTextInterface,
  SiteLayoutInterface
} from "../Interfaces/SiteLayoutInterface";
import {htmlEncode} from "../Utility/TransformUtility";

export class SiteLayout implements SiteLayoutInterface {
  constructor(
    public textColor: string,
    public bgColor: string,
    public fontFamily: FontFamilies,
    public elements: SiteElementInterface<SiteElements>[]
  ) {}

  get textColorEncoded(): string {
    return htmlEncode(this.textColor);
  }

  get bgColorEncoded(): string {
    return htmlEncode(this.bgColor);
  }

  getJSONString(): string {
    return JSON.stringify(this.toJSON());
  }

  toJSON(): SiteLayoutInterface {
    return <SiteLayoutInterface>{
      textColor: this.textColorEncoded,
      bgColor: this.bgColorEncoded,
      fontFamily: this.fontFamily,
      elements: this.elements.map((element: SiteElementInterface<SiteElements>) => element.toJSON())
    };
  }
}

export class SiteElement<T> implements SiteElementInterface<T> {
  constructor(
    public uid: string,
    public type: SiteElementsTypes,
    public element: T
  ) {}

  toJSON(): SiteElementInterface<T> {
    return <SiteElementInterface<T>>{
      uid: this.uid,
      type: this.type,
      element: this.element
    }
  }
}

export class SiteElementHeadline implements SiteElementHeadlineInterface {
  constructor(
    public layout: number,
    public value: string
  ) {}

  get valueEncoded(): string {
    return htmlEncode(this.value);
  }

  toJSON(): SiteElementHeadlineInterface {
    return <SiteElementHeadlineInterface>{
      layout: this.layout,
      value: this.valueEncoded
    };
  }
}

export class SiteElementText implements SiteElementTextInterface {
  constructor(public value: string) {}

  get valueEncoded(): string {
    return htmlEncode(this.value);
  }

  toJSON(): SiteElementTextInterface {
    return <SiteElementTextInterface>{
      value: this.valueEncoded
    };
  }
}

export class SiteElementImage implements SiteElementImageInterface {
  constructor(public imageData: string) {}

  // No encoding needed for imageData
  toJSON(): SiteElementImageInterface {
    return <SiteElementImageInterface>{
      imageData: this.imageData
    };
  }
}

export class SiteElementLink implements SiteElementLinkInterface {
  constructor(
    public title: string,
    public href: string
  ) {}

  get titleEncoded(): string {
    return htmlEncode(this.title);
  }

  toJSON(): SiteElementLinkInterface {
    return <SiteElementLinkInterface>{
      title: this.titleEncoded,
      href: this.href
    };
  }
}
