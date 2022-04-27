import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = {
    url: String
  }

  sort(event) {
    window.location.href = this.urlValue + "?sort=" + event.target.value;
  }

}
