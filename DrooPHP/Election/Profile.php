<?php
/**
 * @file
 *   DrooPHP_Election_Profile class.
 * @package
 *   DrooPHP
 */

/**
 * @class
 *   DrooPHP_Election_Profile
 *   Container for an election profile, and the main parser for BLT file data.
 *
 *   The public interface of ElectionProfile:
 *     $title: title string from the ballot file
 *     $source: source string from blt file
 *     $comment: comment string from blt file
 *     $nSeats: the number of seats to be filled
 *     $nBallots: the number of ballots (possibly greater than len(rankings) because of
 *               ballot multipliers)
 *     $eligible: the set of non-withdrawn candidate IDs
 *     $withdrawn: the set of withdrawn candidate IDs
 *         eligible and withdrawn should be treated as frozenset (unordered and immutable)
 *         though they may be implemented as any iterable.
 *     $ballotLines: a list of BallotLine objects with not equal rankings, each with a:
 *        multiplier: a repetition count >=1
 *        ranking: an array of candidate IDs
 *     $ballotLinesequal: a list of BallotLine objects with at least one equal ranking, each with a:
 *        multiplier: a repetition count >=1
 *        ranking: tuple of tuples of candidate IDs
 *     $tieOrder[cid]: tiebreaking order, by CID
 *     $nickName[cid]: short name of candidate, by CID
 *     $options: list of election options from ballot file
 *     $candidateName[cid]  full name of candidate, by CID
 *     $candidateOrder[cid] ballot order of candidate, by CID
 *   All attributes should be treated as immutable.
 */
class DrooPHP_Election_Profile {

  /** @var string */
  public $title;
  /** @var string */
  public $source;
  /** @var string */
  public $comment;

  /**
   * The number of seats (vacancies) to be filled.
   * @var int
   */
  protected $_num_seats;

  /**
   * The total number of candidates standing.
   * @var int
   */
  protected $_num_candidates;

  /**
   * The set of withdrawn candidate IDs.
   * @var array
   */
  protected $_withdrawn = array();

  /**
   * Array of DrooPHP_Election_Candidate objects.
   *
   * @var array
   */
  protected $_candidates = array();

  /* SETTER METHODS */

  /**
   * Set the number of candidates.
   */
  public function setNumCandidates($int) {
    if (!is_numeric($int)) {
      throw new DrooPHP_Exception('The number of candidates must be an integer.');
    }
    $this->_num_candidates = (int) $int;
  }

  /**
   * Set the number of seats.
   */
  public function setNumSeats($int) {
    if (!is_numeric($int)) {
      throw new DrooPHP_Exception('The number of seats must be an integer.');
    }
    $this->_num_seats = (int) $int;
  }

  /**
   * Mark candidate IDs as withdrawn.
   *
   * @todo validate this after the candidates are added.
   */
  public function setWithdrawn(array $ids) {
    $this->_withdrawn = $ids;
  }

  /**
   * Add a candidate.
   *
   * @param int $id
   * @param string $name
   */
  public function addCandidate($id, $name) {
    $this->_candidates[(int) $id] = new DrooPHP_Election_Candidate($name);
  }

}