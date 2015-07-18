function StatsFC_NextFixture(key) {
	this.domain			= 'https://api.statsfc.com';
	this.referer		= '';
	this.key			= key;
	this.team			= '';
	this.competition	= '';
	this.date			= '';
	this.timezone		= '';

	var $j = jQuery;

	this.display = function(placeholder) {
		if (placeholder.length == 0) {
			return;
		}

		var $placeholder = $j('#' + placeholder);

		if ($placeholder.length == 0) {
			return;
		}

		if (this.referer.length == 0) {
			this.referer = window.location.hostname;
		}

		var $container = $j('<div>').addClass('sfc_nextfixture');

		// Store globals variables here so we can use it later.
		var domain = this.domain;
		var date   = this.date;

		$j.getJSON(
			domain + '/crowdscores/next-fixture.php?callback=?',
			{
				key:			this.key,
				domain:			this.referer,
				team:			this.team,
				competition:	this.competition,
				date:			this.date,
				timezone:		this.timezone
			},
			function(data) {
				if (data.error) {
					$container.append(
						$j('<p>').css('text-align', 'center').append(
							$j('<a>').attr({ href: 'https://statsfc.com', title: 'Football widgets', target: '_blank' }).text('StatsFC.com'),
							' – ',
							data.error
						)
					);

					return;
				}

				var $status = $j('<span>');

				if (! data.match.started || date.length > 0) {
					$status.append(
						$j('<span>').addClass('sfc_date').text(data.match.date),
						$j('<br>'),
						$j('<span>').addClass('sfc_time').text(data.match.time)
					);
				} else {
					$status.append(
						$j('<span>').html('<small>Live: ' + data.match.status + '</small><br>' + data.match.score[0] + ' - ' + data.match.score[1])
					);
				}

				var $table = $j('<table>').append(
					$j('<tbody>').append(
						$j('<tr>').append(
							$j('<td>').addClass('sfc_home sfc_badge_' + data.match.homepath).append(
								$j('<img>').attr({ src: 'https://api.statsfc.com/kit/' + data.match.homepath + '.svg', title: data.match.home, width: 80, height: 80 }),
								$j('<br>'),
								$j('<span>').addClass('sfc_team').text(data.match.home)
							),
							$j('<td>').addClass('sfc_details').append(
								$j('<span>').addClass('sfc_competition').text(data.match.competition),
								$j('<br>'),
								$status
							),
							$j('<td>').addClass('sfc_away sfc_badge_' + data.match.awaypath).append(
								$j('<img>').attr({ src: 'https://api.statsfc.com/kit/' + data.match.awaypath + '.svg', title: data.match.away, width: 80, height: 80 }),
								$j('<br>'),
								$j('<span>').addClass('sfc_team').text(data.match.away)
							)
						)
					)
				);

				$container.append($table);

				if (data.customer.attribution) {
					$container.append(
						$j('<div>').attr('class', 'sfc_footer').append(
							$j('<p>').append(
								$j('<small>').append('Powered by ').append(
									$j('<a>').attr({ href: 'https://statsfc.com', title: 'StatsFC – Football widgets', target: '_blank' }).text('StatsFC.com')
								).append('. Fan data via ').append(
									$j('<a>').attr({ href: 'https://crowdscores.com', title: 'CrowdScores', target: '_blank' }).text('CrowdScores.com')
								)
							)
						)
					);
				}
			}
		);

		$j('#' + placeholder).append($container);
	};
}
