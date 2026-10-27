-- සිංහල සටහන: කලින් database එක import කරලා තිබේ නම්, real JPG images path update කරගැනීමට මෙම SQL එක run කරන්න.
USE art_gallery;

UPDATE artists SET image='assets/images/artist-ethan.jpg' WHERE name='Ethan Mitchell';
UPDATE artists SET image='assets/images/artist-lucas.jpg' WHERE name='Lucas Brown';
UPDATE artists SET image='assets/images/artist-oliver.jpg' WHERE name='Oliver Taylor';
UPDATE artists SET image='assets/images/artist-amara.jpg' WHERE name='Amara Silva';

UPDATE artworks SET image='assets/images/autumn.jpg' WHERE slug='whispers-of-autumn';
UPDATE artworks SET image='assets/images/floating-worlds.jpg' WHERE slug='floating-worlds';
UPDATE artworks SET image='assets/images/tranquil-oasis.jpg' WHERE slug='tranquil-oasis';
UPDATE artworks SET image='assets/images/midnight-bloom.jpg' WHERE slug='midnight-bloom';
UPDATE artworks SET image='assets/images/golden-memory.jpg' WHERE slug='golden-memory';
UPDATE artworks SET image='assets/images/sea-of-lines.jpg' WHERE slug='sea-of-lines';

UPDATE exhibitions SET image='assets/images/exhibition-01.jpg' WHERE title='New Forms in Modern Art';
UPDATE exhibitions SET image='assets/images/custom-work.jpg' WHERE title='Collectors Private Viewing';
