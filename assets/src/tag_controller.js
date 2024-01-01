import { Controller } from '@hotwired/stimulus';
import tagger from '@jcubic/tagger';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['tags'];
  static values = {
    autoComList: Array,
  }

  connect() {
    const input = this.tagsTarget;

    //const tags =
    tagger(input, {
      allow_duplicates: false,
      allow_spaces: true,
      wrap: true,
      link: function() {return false},
      tag_limit: 4,
      completion: {
        list: this.autoComListValue
      }
    });

  }

}
