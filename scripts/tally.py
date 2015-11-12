#!/usr/bin/python
# usage: 
# 1. need to ssh into scripts. first ssh into athena, then ssh lingapps@scripts.mit.edu
# 2. cd colloq/scripts
# 3. python tally.py 

import os

if 'REQUEST_METHOD' in os.environ:
	print "Content-type: text/html\n"

######################################################################
# Imports

from bisect import *

######################################################################
# Globals

SCORING = [10, 7, 4, 2, 1]

LIST_SIZE = 3

######################################################################
# Establish connection(s)

import MySQLdb
import MySQLdb.cursors
import ConfigParser
config = ConfigParser.RawConfigParser()
config.read('../../../.sql/my.cnf')

hostname_cnvs = config.get('client', 'host')
database_cnvs = "lingapps+colloq"
username_cnvs = config.get('client', 'user')
password_cnvs = config.get('client', 'password')

cnvs_list = MySQLdb.connect(host=hostname_cnvs, user=username_cnvs, passwd=password_cnvs, db=database_cnvs, cursorclass=MySQLdb.cursors.Cursor)
cnvs_dict = MySQLdb.connect(host=hostname_cnvs, user=username_cnvs, passwd=password_cnvs, db=database_cnvs, cursorclass=MySQLdb.cursors.DictCursor)
 
######################################################################
# Collect ballots from database

cur = cnvs_list.cursor()
ballots = []
for i in xrange(cur.execute('SELECT nom1, nom2, nom3, nom4, nom5 FROM ballots')):
	orig_votes = list(cur.fetchone())
	votes = []
	for nom in orig_votes:
		if nom not in votes and nom >= 0:
			votes.append(nom)
	ballots.append(votes)

scores = {}
for ballot in ballots:
	try:
		for i in xrange(5):
			nom = ballot[i]
			scores[nom] = scores.get(nom, 0) + SCORING[i]
	except IndexError:
		continue

cur.close()

######################################################################
# Collect nominees from database

cur = cnvs_dict.cursor()
nominees_by_id = {}
cur.execute('SELECT id, lastname, firstname, affiliation, syntax, semantics, phonology FROM nominees')
for nom in cur.fetchall():
	nom = dict(nom)
	score = scores.get(nom['id'], 0)
	nom['score'] = score
	nominees_by_id[nom['id']] = nom

def sorted_noms(noms_by_id):
	scores_and_ids = [(nom['score'], id) for id, nom in noms_by_id.items()]
	scores_and_ids.sort()
	return [noms_by_id[id] for (score, id) in scores_and_ids]

sorted_nominees = sorted_noms(nominees_by_id)

cur.close()

######################################################################
# Collect choices by category

syntax_choice = []
semantics_choice = []
phonology_choice = []
other_choice = []

def get_bound(choice):
	if len(choice) < LIST_SIZE:
		return 0
	else:
		return choice[-1]['score']

def cond_add(choice, nom):
	bound = get_bound(choice)
	if nom['score'] < bound:
		return
	
	while len(choice) >= LIST_SIZE and nom['score'] > choice[-1]['score']:
		choice.pop()
	
	choice.reverse()
	scores_and_noms = [(c['score'], c) for c in choice]
	choice.insert(bisect(scores_and_noms, (nom['score'], nom)), nom)
	choice.reverse()

for nom in sorted_nominees:
	cond_add(other_choice, nom)
	if nom['syntax']:
		cond_add(syntax_choice, nom)
	if nom['semantics']:
		cond_add(semantics_choice, nom)
	if nom['phonology']:
		cond_add(phonology_choice, nom)

######################################################################
# Utilities for printing results

def print_nom(nom, highlight=False):
	if 'REQUEST_METHOD' in os.environ:
		print '<p>'
		chosen = nom in syntax_choice or nom in semantics_choice or nom in phonology_choice or nom in other_choice
		if highlight and chosen:
			print '<strong>'
		print '%(firstname)s %(lastname)s [%(affiliation)s] (%(score)s)' % nom
		if highlight and chosen:
			print '</strong>'
		print '</p>'
	else:
		print '%(firstname)s %(lastname)s [%(affiliation)s] (%(score)s)' % nom

def print_cat(cat):
	for nom in cat:
		print_nom(nom)

def print_all():
	if 'REQUEST_METHOD' in os.environ:
		print '<h3>Syntax:</h3>'
	else:
		print
		print 'Syntax:'
		print '--------'
	print_cat(syntax_choice)

	if 'REQUEST_METHOD' in os.environ:
		print '<h3>Semantics:</h3>'
	else:
		print
		print 'Semantics:'
		print '-----------'
	print_cat(semantics_choice)

	if 'REQUEST_METHOD' in os.environ:
		print '<h3>Phonology:</h3>'
	else:
		print
		print 'Phonology:'
		print '-----------'
	print_cat(phonology_choice)

	if 'REQUEST_METHOD' in os.environ:
		print '<h3>Other:</h3>'
	else:
		print
		print 'Other:'
		print '-------'
	print_cat(other_choice)

	if 'REQUEST_METHOD' in os.environ:
		print '<p>'
	print
	print '(based on %d ballots)' % len(ballots)
	print
	if 'REQUEST_METHOD' in os.environ:
		print '</p>'

######################################################################
# Force disjointness of lists

def trim_sets(*cat_list):
	for cat in cat_list:
		if len(cat) < LIST_SIZE:
			continue
		while cat[-1]['score'] < cat[LIST_SIZE-1]['score']:
			cat.pop()

def first_common(*cat_list):
	ids = {}
	for cat in cat_list:
		for nom in cat:
			if ids.has_key(nom['id']):
				return nom
			ids[nom['id']] = True
	
	return None

def extend_choice(new_choice, replacements):
	replacements = replacements[:]
	new_choice.append(replacements.pop())
	while new_choice[-1]['score'] == replacements[-1]['score']:
		new_choice.append(replacements.pop())

trim_sets(syntax_choice, semantics_choice, phonology_choice, other_choice)
common = first_common(syntax_choice, semantics_choice, phonology_choice, other_choice)
while common:
	repl_sets = []
	already_in = syntax_choice + semantics_choice + phonology_choice + other_choice
	if common in syntax_choice:
		replacements = [nom for nom in sorted_nominees if nom['syntax'] and nom not in already_in]
		new_choice = syntax_choice[:]
		new_choice.remove(common)
		extend_choice(new_choice, replacements)
		repl_sets.append((new_choice[-1]['score'], 0, (new_choice, semantics_choice, phonology_choice, other_choice)))
	if common in semantics_choice:
		replacements = [nom for nom in sorted_nominees if nom['semantics'] and nom not in already_in]
		new_choice = semantics_choice[:]
		new_choice.remove(common)
		extend_choice(new_choice, replacements)
		repl_sets.append((new_choice[-1]['score'], 1, (syntax_choice, new_choice, phonology_choice, other_choice)))
	if common in phonology_choice:
		replacements = [nom for nom in sorted_nominees if nom['phonology'] and nom not in already_in]
		new_choice = phonology_choice[:]
		new_choice.remove(common)
		extend_choice(new_choice, replacements)
		repl_sets.append((new_choice[-1]['score'], 2, (syntax_choice, semantics_choice, new_choice, other_choice)))
	if common in other_choice:
		replacements = [nom for nom in sorted_nominees if nom not in already_in]
		new_choice = other_choice[:]
		new_choice.remove(common)
		extend_choice(new_choice, replacements)
		repl_sets.append((new_choice[-1]['score'], 3, (syntax_choice, semantics_choice, phonology_choice, new_choice)))
	repl_sets.sort()
	score, dummy, (syntax_choice, semantics_choice, phonology_choice, other_choice) = repl_sets[-1]
	trim_sets(syntax_choice, semantics_choice, phonology_choice, other_choice)
	common = first_common(syntax_choice, semantics_choice, phonology_choice, other_choice)

######################################################################
# End result

print_all()

if 'REQUEST_METHOD' in os.environ:
	print '<h3>Raw counts:</h3>'

print
for nom in sorted_nominees:
	print_nom(nom, True)
print
