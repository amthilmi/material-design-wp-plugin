/**
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * External dependencies
 */
import { get, pickBy } from 'lodash';

/**
 * WordPress dependencies
 */
import { withSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import './style.css';
import NoPosts from './components/no-posts';
import PostsList from './components/posts-list';

/**
 * Edit component.
 *
 * @param {Object} props - Component props.
 *
 * @return {Function} A functional component.
 */
const Edit = props => {
	const { postsToDisplay } = props;
	const hasPosts = Array.isArray( postsToDisplay ) && postsToDisplay.length;

	if ( ! hasPosts ) {
		return <NoPosts { ...props } />;
	}

	return <PostsList { ...props } />;
};

/**
 * @type {Function} A functional component.
 */
const EditWithSelectMultiplePostTypes = withSelect( ( select, props ) => {
	const {
		category,
		postsToShow,
		posts,
		orderby,
		displayFeaturedImage,
		style,
	} = props.attributes;
	const { name } = props;

	const featuredImageSizeSlug = style === 'list' ? 'medium' : 'large';

	const { getMedia } = select( 'core' );

	const queryArgs = {
		categories: category,
		include: posts.map( Number ),
		per_page: posts.length,
		orderby: 'date',
		order: 'desc',
	};

	if ( orderby ) {
		if ( orderby === 'title' ) {
			queryArgs.orderby = 'title';
			queryArgs.order = 'asc';
		} else if ( orderby === 'popularity' ) {
			queryArgs.orderby = 'comment_count';
			queryArgs.order = 'desc';
		}
	}

	const fetchedPosts = apiFetch( {
		path: '/material-design/v1/post-types/get-posts',
		method: 'GET',
		data: queryArgs,
	} )
		.then( data => {
			return data;
		} )
		.catch( e => {
			throw e;
		} );

	return {
		postsToDisplay: ! Array.isArray( fetchedPosts )
			? fetchedPosts
			: fetchedPosts.map( post => {
					if ( displayFeaturedImage && post.featured_media ) {
						const image = getMedia( post.featured_media );
						let url = get(
							image,
							[ 'media_details', 'sizes', featuredImageSizeSlug, 'source_url' ],
							null
						);
						if ( ! url ) {
							url = get( image, 'source_url', null );
						}
						return { ...post, featuredImageSourceUrl: url };
					}
					return post;
			  } ),
	};
} )( Edit );

export default EditWithSelectMultiplePostTypes;
